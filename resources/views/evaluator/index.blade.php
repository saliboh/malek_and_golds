<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Receipt Evaluator - Malek & Golds</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; }
        .safe-area-top { padding-top: max(1rem, env(safe-area-inset-top)); }
        .safe-area-bottom { padding-bottom: max(1rem, env(safe-area-inset-bottom)); }
        .safe-area-left { padding-left: max(1rem, env(safe-area-inset-left)); }
        .safe-area-right { padding-right: max(1rem, env(safe-area-inset-right)); }
    </style>
</head>
<body class="bg-gradient-to-br from-emerald-50 via-white to-teal-50 safe-area-top safe-area-bottom">
<div class="min-h-screen flex flex-col lg:flex-row">

    @include('layouts.navigation')

    <div class="flex-1 flex flex-col">
        <!-- Header -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white sticky top-0 z-10 shadow-lg">
            <div class="px-4 py-4">
                <h1 class="text-2xl font-bold">⚖️ Receipt Evaluator</h1>
                <p class="text-emerald-100 text-sm mt-0.5">
                    @if($todayGoldPrice)
                        Today's rate: <strong>₱{{ number_format($todayGoldPrice->daily_price, 0) }}/g</strong>
                    @else
                        <span class="text-red-300 font-semibold">⚠️ No gold price set for today</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 px-4 py-5 max-w-2xl mx-auto w-full space-y-4">

            @if(!$todayGoldPrice)
            <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4">
                <p class="text-red-800 font-semibold text-sm">⚠️ No gold price found for today. Please set it in Admin before evaluating.</p>
            </div>
            @endif

            <!-- Receipts Container -->
            <div id="receiptsContainer" class="space-y-3"></div>

            <!-- Add Receipt Button -->
            <button onclick="addReceipt()" class="w-full border-2 border-dashed border-emerald-400 text-emerald-700 font-bold py-3 rounded-xl text-sm hover:bg-emerald-50 transition flex items-center justify-center gap-2">
                + Add Receipt
            </button>

            <!-- Evaluate Button -->
            <button onclick="runEvaluate()" class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold py-4 rounded-xl text-base shadow-md transition">
                ⚖️ Evaluate All Receipts
            </button>

            <!-- Results -->
            <div id="results" class="hidden space-y-4">

                <!-- Summary Card -->
                <div id="summaryCard" class="bg-white rounded-xl shadow border-2 border-emerald-300 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-4 py-3">
                        <p class="text-white font-bold text-sm uppercase tracking-wide">📊 Summary</p>
                    </div>
                    <div class="p-4 space-y-2 text-sm" id="summaryContent"></div>
                </div>

                <!-- Per-Receipt Breakdown -->
                <div id="receiptBreakdown" class="space-y-3"></div>

            </div>

        </div>
    </div>
</div>

<script>
    const todayRate = {{ $todayGoldPrice?->daily_price ?? 0 }};
    const karats = {!! json_encode($karats->map(fn($k) => ['id' => $k->id, 'karat_value' => $k->karat_value, 'multiplier' => $k->multiplier])) !!};
    let receiptCount = 0;

    function karatOptions(selectedId = null) {
        return karats.map(k =>
            `<option value="${k.id}" data-multiplier="${k.multiplier}" ${k.id == selectedId ? 'selected' : ''}>${k.karat_value}K</option>`
        ).join('');
    }

    function addReceipt() {
        receiptCount++;
        const id = receiptCount;
        const container = document.getElementById('receiptsContainer');

        const div = document.createElement('div');
        div.id = `receipt-${id}`;
        div.className = 'bg-white rounded-xl shadow border border-gray-200 overflow-hidden';
        div.innerHTML = `
            <div class="bg-gray-800 px-4 py-2.5 flex items-center justify-between">
                <p class="text-white font-bold text-sm">📄 Receipt ${id}</p>
                <button onclick="removeReceipt(${id})" class="text-gray-400 hover:text-red-400 text-lg font-bold leading-none">×</button>
            </div>
            <div class="p-3 space-y-3">
                <!-- Items -->
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Gold Items</p>
                    <div id="items-${id}" class="space-y-2"></div>
                    <button onclick="addItem(${id})" class="mt-2 text-xs text-emerald-600 font-bold border border-emerald-300 px-3 py-1.5 rounded-lg hover:bg-emerald-50 transition">
                        + Add Item
                    </button>
                </div>
                <!-- Lukat -->
                <div class="space-y-2">
                    <!-- Mode Toggle -->
                    <div class="flex gap-2">
                        <button onclick="setLukatMode(${id}, 'manual')" id="lukat-mode-manual-${id}"
                            class="flex-1 text-xs font-bold py-1.5 rounded-lg border-2 border-amber-400 bg-amber-400 text-white transition">
                            ✏️ Manual
                        </button>
                        <button onclick="setLukatMode(${id}, 'estimate')" id="lukat-mode-estimate-${id}"
                            class="flex-1 text-xs font-bold py-1.5 rounded-lg border-2 border-amber-200 text-amber-600 bg-white transition">
                            📅 Estimate
                        </button>
                    </div>

                    <!-- Manual Input -->
                    <div id="lukat-manual-${id}" class="flex items-center gap-2 bg-amber-50 rounded-lg px-3 py-2 border border-amber-200">
                        <span class="text-xs font-semibold text-amber-700 whitespace-nowrap">Lukat Fee ₱</span>
                        <input type="number" id="lukat-${id}" placeholder="0" min="0" step="1"
                            class="flex-1 bg-transparent text-amber-800 font-bold text-sm focus:outline-none placeholder-amber-300 min-w-0">
                    </div>

                    <!-- Estimate Input -->
                    <div id="lukat-estimate-${id}" class="hidden bg-amber-50 rounded-lg p-3 border border-amber-200 space-y-2">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-semibold text-amber-700">📅 Lukat Checkpoints</p>
                            <button onclick="addCheckpoint(${id})" class="text-xs text-amber-600 font-bold border border-amber-300 px-2 py-0.5 rounded hover:bg-amber-100">+ Add</button>
                        </div>
                        <div id="checkpoints-${id}" class="space-y-1.5"></div>
                        <div class="space-y-1">
                            <p class="text-xs text-amber-600 font-semibold">Estimate lukat on what date?</p>
                            <input type="date" id="estimate-date-${id}" value="${new Date().toISOString().split('T')[0]}"
                                class="w-full px-2 py-1.5 border border-amber-300 rounded-lg text-sm font-bold text-amber-800 focus:outline-none focus:border-amber-500 bg-white">
                        </div>
                        <button onclick="estimateLukat(${id})"
                            class="w-full text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white py-1.5 rounded-lg transition">
                            ⚡ Compute Estimate
                        </button>
                        <div id="lukat-estimate-result-${id}" class="hidden text-center py-2 bg-white rounded-lg border-2 border-amber-300">
                            <p class="text-xs text-amber-500 font-semibold" id="lukat-estimate-label-${id}">—</p>
                            <p class="text-xl font-black text-amber-700" id="lukat-estimate-value-${id}">₱0</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(div);
        addItem(id);

        // Hide results on change
        document.getElementById('results').classList.add('hidden');
    }

    function removeReceipt(id) {
        const el = document.getElementById(`receipt-${id}`);
        if (el) el.remove();
        document.getElementById('results').classList.add('hidden');
    }

    function addItem(receiptId) {
        const container = document.getElementById(`items-${receiptId}`);
        const itemId = Date.now();
        const div = document.createElement('div');
        div.id = `item-${itemId}`;
        div.className = 'flex gap-2 items-center';
        div.innerHTML = `
            <input type="number" step="0.01" min="0.01" placeholder="Grams"
                class="flex-1 px-2.5 py-2 border border-gray-200 rounded-lg text-sm focus:border-emerald-400 focus:outline-none min-w-0">
            <select class="w-24 px-2 py-2 border border-gray-200 rounded-lg text-sm focus:border-emerald-400 focus:outline-none bg-white">
                ${karatOptions()}
            </select>
            <button onclick="document.getElementById('item-${itemId}').remove()" class="text-gray-300 hover:text-red-400 text-xl font-bold leading-none flex-shrink-0">×</button>
        `;
        container.appendChild(div);
    }

    function runEvaluate() {
        if (todayRate <= 0) {
            alert('No gold price set for today. Please add it in Admin first.');
            return;
        }

        const receiptDivs = document.querySelectorAll('[id^="receipt-"]');
        if (receiptDivs.length === 0) {
            alert('Please add at least one receipt.');
            return;
        }

        const receiptResults = [];
        let totalGoldValue = 0;
        let totalLukat = 0;
        let hasError = false;

        receiptDivs.forEach((div, idx) => {
            const receiptId = div.id.replace('receipt-', '');
            const label = `Receipt ${idx + 1}`;
            const lukatInput = document.getElementById(`lukat-${receiptId}`);
            const lukat = parseFloat(lukatInput?.value || 0) || 0;
            const manualDiv = document.getElementById(`lukat-manual-${receiptId}`);
            const isEstimated = manualDiv ? manualDiv.classList.contains('hidden') : false;
            const estimatedLabel = isEstimated
                ? document.getElementById(`lukat-estimate-label-${receiptId}`)?.textContent || 'estimated'
                : null;

            const itemRows = document.querySelectorAll(`#items-${receiptId} > div`);
            const items = [];
            let receiptGoldValue = 0;

            itemRows.forEach(row => {
                const gramsInput = row.querySelector('input[type="number"]');
                const karatSelect = row.querySelector('select');
                const grams = parseFloat(gramsInput?.value || 0);
                const multiplier = parseFloat(karatSelect?.selectedOptions[0]?.dataset?.multiplier || 0);
                const karatVal = karatSelect?.selectedOptions[0]?.text || '';

                if (grams > 0 && multiplier > 0) {
                    const pricePerGram = todayRate * multiplier;
                    const itemValue = pricePerGram * grams;
                    receiptGoldValue += itemValue;
                    items.push({ grams, karat: karatVal, pricePerGram, itemValue });
                }
            });

            if (items.length === 0) {
                hasError = true;
                alert(`${label} has no valid items. Please fill in grams and karat.`);
                return;
            }

            const profit = receiptGoldValue - lukat;
            totalGoldValue += receiptGoldValue;
            totalLukat += lukat;

            receiptResults.push({ label, items, lukat, isEstimated, estimatedLabel, receiptGoldValue, profit, isLosing: profit < 0 });
        });

        if (hasError) return;

        const totalProfit = totalGoldValue - totalLukat;

        // Render summary
        const summaryEl = document.getElementById('summaryContent');
        summaryEl.innerHTML = `
            <div class="flex justify-between items-center p-2.5 bg-blue-50 rounded-lg">
                <span class="font-semibold text-gray-700">Total Gold Value</span>
                <span class="font-black text-blue-600">₱${fmt(totalGoldValue)}</span>
            </div>
            <div class="flex justify-between items-center p-2.5 bg-amber-50 rounded-lg">
                <span class="font-semibold text-gray-700">Total Lukat</span>
                <span class="font-black text-amber-600">-₱${fmt(totalLukat)}</span>
            </div>
            <div class="flex justify-between items-center p-2.5 rounded-lg border-2 ${totalProfit < 0 ? 'bg-red-50 border-red-400' : 'bg-emerald-50 border-emerald-400'}">
                <span class="font-bold text-gray-800">Net Profit</span>
                <span class="font-black text-xl ${totalProfit < 0 ? 'text-red-600' : 'text-emerald-600'}">₱${fmt(totalProfit)}</span>
            </div>
            ${totalProfit < 0 ? `<p class="text-xs text-red-600 font-semibold text-center">⚠️ Overall transaction results in a loss</p>` : ''}
        `;

        // Render per-receipt breakdown
        const breakdownEl = document.getElementById('receiptBreakdown');
        breakdownEl.innerHTML = '';

        receiptResults.forEach(r => {
            const card = document.createElement('div');
            card.className = `rounded-xl shadow overflow-hidden border-2 ${r.isLosing ? 'border-red-400' : 'border-gray-200'}`;
            card.innerHTML = `
                <div class="px-4 py-2.5 flex items-center justify-between ${r.isLosing ? 'bg-red-600' : 'bg-gray-700'}">
                    <p class="text-white font-bold text-sm">📄 ${r.label}</p>
                    ${r.isLosing ? `<span class="text-xs bg-white/20 text-white font-bold px-2 py-0.5 rounded-full">⚠️ Losing</span>` : `<span class="text-xs bg-white/10 text-gray-200 font-semibold px-2 py-0.5 rounded-full">✅ Profitable</span>`}
                </div>
                <div class="bg-white p-3 space-y-2 text-sm">
                    <!-- Items -->
                    <div class="space-y-1">
                        ${r.items.map((item, i) => `
                        <div class="flex justify-between items-center p-2 bg-gray-50 rounded-lg">
                            <span class="text-gray-700 font-semibold">${item.grams.toFixed(2)}g ${item.karat} <span class="text-xs text-gray-400 font-normal">@ ₱${fmt(item.pricePerGram)}/g</span></span>
                            <span class="font-bold text-blue-600">₱${fmt(item.itemValue)}</span>
                        </div>`).join('')}
                    </div>
                    <!-- Totals -->
                    <div class="flex justify-between items-center p-2 bg-blue-50 rounded-lg border-l-4 border-blue-400">
                        <span class="font-semibold text-gray-700">Gold Value</span>
                        <span class="font-black text-blue-600">₱${fmt(r.receiptGoldValue)}</span>
                    </div>
                    <div class="flex justify-between items-center p-2 bg-amber-50 rounded-lg border-l-4 border-amber-400">
                        <span class="font-semibold text-gray-700">Lukat Fee ${r.isEstimated ? '<span class="text-xs text-amber-500 font-normal">(estimated)</span>' : ''}</span>
                        <span class="font-black text-amber-600">-₱${fmt(r.lukat)}</span>
                    </div>
                    <div class="flex justify-between items-center p-2.5 rounded-lg border-2 ${r.isLosing ? 'bg-red-50 border-red-400' : 'bg-emerald-50 border-emerald-400'}">
                        <span class="font-bold">Profit</span>
                        <span class="font-black text-lg ${r.isLosing ? 'text-red-600' : 'text-emerald-600'}">₱${fmt(r.profit)}</span>
                    </div>
                    ${r.isLosing ? `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-2.5 text-center">
                        <p class="text-xs text-red-700 font-bold">⚠️ Lukat (₱${fmt(r.lukat)}) exceeds gold melt value (₱${fmt(r.receiptGoldValue)})</p>
                        <p class="text-xs text-red-600 mt-0.5">Loss: ₱${fmt(Math.abs(r.profit))}</p>
                    </div>` : ''}
                </div>
            `;
            breakdownEl.appendChild(card);
        });

        document.getElementById('results').classList.remove('hidden');
        document.getElementById('results').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function setLukatMode(id, mode) {
        const manualBtn  = document.getElementById(`lukat-mode-manual-${id}`);
        const estimateBtn = document.getElementById(`lukat-mode-estimate-${id}`);
        const manualDiv  = document.getElementById(`lukat-manual-${id}`);
        const estimateDiv = document.getElementById(`lukat-estimate-${id}`);

        if (mode === 'manual') {
            manualBtn.classList.add('bg-amber-400', 'text-white', 'border-amber-400');
            manualBtn.classList.remove('bg-white', 'text-amber-600', 'border-amber-200');
            estimateBtn.classList.remove('bg-amber-400', 'text-white', 'border-amber-400');
            estimateBtn.classList.add('bg-white', 'text-amber-600', 'border-amber-200');
            manualDiv.classList.remove('hidden');
            estimateDiv.classList.add('hidden');
        } else {
            estimateBtn.classList.add('bg-amber-400', 'text-white', 'border-amber-400');
            estimateBtn.classList.remove('bg-white', 'text-amber-600', 'border-amber-200');
            manualBtn.classList.remove('bg-amber-400', 'text-white', 'border-amber-400');
            manualBtn.classList.add('bg-white', 'text-amber-600', 'border-amber-200');
            estimateDiv.classList.remove('hidden');
            manualDiv.classList.add('hidden');
            // Add 2 starter checkpoints if empty
            const container = document.getElementById(`checkpoints-${id}`);
            if (container.children.length === 0) {
                addCheckpoint(id);
                addCheckpoint(id);
            }
        }
    }

    function addCheckpoint(id) {
        const container = document.getElementById(`checkpoints-${id}`);
        const cpId = Date.now();
        const div = document.createElement('div');
        div.id = `cp-${cpId}`;
        div.className = 'flex gap-1.5 items-center';
        div.innerHTML = `
            <input type="date" class="flex-1 px-2 py-1.5 border border-amber-200 rounded-lg text-xs focus:outline-none focus:border-amber-400 bg-white min-w-0 cp-date">
            <div class="flex items-center border border-amber-200 rounded-lg bg-white px-2 py-1.5 gap-1 flex-1 min-w-0">
                <span class="text-xs text-amber-600 font-bold">₱</span>
                <input type="number" min="0" step="1" placeholder="Amount" class="flex-1 text-xs font-bold text-amber-800 focus:outline-none bg-transparent min-w-0 cp-amount">
            </div>
            <button onclick="document.getElementById('cp-${cpId}').remove()" class="text-gray-300 hover:text-red-400 text-lg font-bold leading-none flex-shrink-0">×</button>
        `;
        container.appendChild(div);
    }

    function estimateLukat(id) {
        const container = document.getElementById(`checkpoints-${id}`);
        const rows = container.querySelectorAll('[id^="cp-"]');

        const targetDateVal = document.getElementById(`estimate-date-${id}`)?.value;
        const today = targetDateVal ? new Date(targetDateVal + 'T00:00:00') : new Date();
        today.setHours(0, 0, 0, 0);

        const points = [];
        rows.forEach(row => {
            const dateVal = row.querySelector('.cp-date')?.value;
            const amtVal  = row.querySelector('.cp-amount')?.value;
            if (dateVal && amtVal && parseFloat(amtVal) > 0) {
                points.push({ date: new Date(dateVal), amount: parseFloat(amtVal) });
            }
        });

        if (points.length === 0) {
            alert('Please enter at least one checkpoint (date + amount).');
            return;
        }

        // Sort by date ascending
        points.sort((a, b) => a.date - b.date);

        let estimated;

        if (points.length === 1) {
            // Only 1 point — use it directly
            estimated = points[0].amount;
        } else {
            // Linear interpolation / extrapolation using the two closest points around today
            // Find the two points surrounding today
            let before = null, after = null;
            for (let i = 0; i < points.length; i++) {
                if (points[i].date <= today) before = points[i];
                if (points[i].date >= today && after === null) after = points[i];
            }

            if (before && after && before.date.getTime() !== after.date.getTime()) {
                // Interpolate between before and after
                const totalMs = after.date - before.date;
                const elapsedMs = today - before.date;
                const ratio = elapsedMs / totalMs;
                estimated = before.amount + ratio * (after.amount - before.amount);
            } else if (before && !after) {
                // Today is after all points — extrapolate from last two
                const p1 = points[points.length - 2];
                const p2 = points[points.length - 1];
                const daysTotal = (p2.date - p1.date) / 86400000;
                const daysFromP2 = (today - p2.date) / 86400000;
                const dailyRate = daysTotal > 0 ? (p2.amount - p1.amount) / daysTotal : 0;
                estimated = p2.amount + dailyRate * daysFromP2;
            } else if (!before && after) {
                // Today is before all points — extrapolate backward from first two
                const p1 = points[0];
                const p2 = points[1];
                const daysTotal = (p2.date - p1.date) / 86400000;
                const daysToP1 = (p1.date - today) / 86400000;
                const dailyRate = daysTotal > 0 ? (p2.amount - p1.amount) / daysTotal : 0;
                estimated = p1.amount - dailyRate * daysToP1;
            } else {
                // Same day point exists
                estimated = before ? before.amount : after.amount;
            }
        }

        estimated = Math.max(0, Math.round(estimated));

        // Write into the hidden lukat input so runEvaluate() picks it up
        const lukatInput = document.getElementById(`lukat-${id}`);
        lukatInput.value = estimated;

        // Format the target date nicely for display
        const displayDate = today.toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' });

        // Show result
        const resultEl = document.getElementById(`lukat-estimate-result-${id}`);
        resultEl.classList.remove('hidden');
        document.getElementById(`lukat-estimate-label-${id}`).textContent = `Lukat on ${displayDate}`;
        document.getElementById(`lukat-estimate-value-${id}`).textContent = '₱' + fmt(estimated);
    }

    function fmt(n) {
        return Number(n).toLocaleString('en-PH', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }

    // Start with one receipt
    addReceipt();
</script>
</body>
</html>

