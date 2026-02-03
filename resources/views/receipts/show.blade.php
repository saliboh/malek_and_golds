<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Receipt Evaluation - Malek & Golds</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; }
        .safe-area-top { padding-top: max(1rem, env(safe-area-inset-top)); }
        .safe-area-bottom { padding-bottom: max(1rem, env(safe-area-inset-bottom)); }
        .safe-area-left { padding-left: max(1rem, env(safe-area-inset-left)); }
        .safe-area-right { padding-right: max(1rem, env(safe-area-inset-right)); }
    </style>
</head>
<body class="bg-gradient-to-br from-green-50 via-white to-emerald-50 safe-area-top safe-area-bottom">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 text-white sticky top-0 z-10 shadow-lg">
            <div class="px-4 safe-area-left safe-area-right py-5">
                <div class="flex items-center justify-between mb-2">
                    <h1 class="text-2xl lg:text-3xl font-bold">💎 Receipt Evaluation</h1>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm bg-black/20 hover:bg-black/30 px-3 py-2 rounded-lg">🚪</button>
                    </form>
                </div>
                <p class="text-green-100 text-sm">Receipt #{{ $receipt->receipt_number }}</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto px-4 safe-area-left safe-area-right py-6 max-w-2xl mx-auto">

            @if (session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
                    <p class="text-green-800 font-semibold">✅ {{ session('success') }}</p>
                </div>
            @endif

            <!-- Edit Toggle Button -->
            <div class="mb-4 flex justify-end gap-2 flex-wrap">
                <a href="{{ route('receipts.printable', $receipt) }}" target="_blank" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                    👁️ View Shareable
                </a>
                <button type="button" onclick="toggleEditMode()" id="editToggleBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                    ✏️ Edit
                </button>
                <form action="{{ route('receipts.update', $receipt) }}" method="POST" id="updateForm" style="display: none;">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="items" id="updateItemsInput">
                    <input type="hidden" name="lukat_fee" id="updateLukatFeeInput">
                    <button type="submit" id="saveBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                        💾 Save Changes
                    </button>
                </form>
                <button type="button" onclick="cancelEditMode()" id="cancelBtn" style="display: none;" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                    ❌ Cancel
                </button>
            </div>

            <!-- Gold Items (Editable) -->
            <div class="bg-white rounded-xl shadow p-6 mb-6 space-y-4">
                <h2 class="font-bold text-lg">Gold Items ({{ count($breakdown) }})</h2>

                <!-- Display Mode -->
                <div class="space-y-2" id="itemsDisplay">
                    @foreach ($breakdown as $index => $item)
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                        <span class="font-semibold">
                            <span class="text-xs bg-blue-600 text-white px-2 py-1 rounded mr-2">Item {{ $index + 1 }}</span>
                            {{ number_format($item['grams'], 2) }}g of {{ $item['karat'] }}K
                        </span>
                        <div class="text-right">
                            <p class="text-xs text-gray-600">₱{{ number_format($item['price_per_gram'], 0) }}/g</p>
                            <p class="font-bold text-blue-600">₱{{ number_format($item['total'], 0) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Edit Mode -->
                <div id="itemsEditMode" style="display: none;" class="space-y-3">
                    <div id="editItemsContainer">
                        @foreach ($receipt->items as $item)
                        <div class="flex gap-2 edit-item-row">
                            <input type="number" step="0.01" value="{{ $item['grams'] }}" placeholder="Grams" class="flex-1 px-3 py-2 border-2 border-gray-200 rounded-lg edit-grams">
                            <select class="w-32 px-3 py-2 border-2 border-gray-200 rounded-lg edit-karat-select">
                                @foreach ($karats as $karat)
                                    <option value="{{ $karat->id }}" {{ $karat->id == $item['karat_id'] ? 'selected' : '' }}>{{ $karat->karat_value }}K</option>
                                @endforeach
                            </select>
                            <button type="button" onclick="removeEditItem(this)" class="bg-red-500 text-white px-3 py-2 rounded-lg">✕</button>
                        </div>
                        @endforeach
                    </div>

                    <button type="button" onclick="addEditItem()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                        + Add Item
                    </button>
                </div>

                <input type="hidden" id="editItemsInput">
            </div>

            <!-- Pawn Lukat Fee (Editable) -->
            <div class="bg-amber-50 rounded-xl shadow border-2 border-amber-300 p-6 mb-6 space-y-4">
                <h2 class="font-bold text-lg">Pawn Lukat Fee</h2>

                <!-- Display Mode -->
                <div id="lukatDisplay" class="flex items-center gap-2 text-2xl">
                    <span>₱</span>
                    <span class="font-bold" id="lukatAmount">{{ number_format($receipt->lukat_fee, 0) }}</span>
                </div>

                <!-- Edit Mode -->
                <div id="lukatEditMode" style="display: none;">
                    <input type="number" id="lukatFeeInput" value="{{ $receipt->lukat_fee }}" step="1" class="w-full px-4 py-3 text-lg border-2 border-amber-400 rounded-lg focus:border-amber-600">
                </div>
            </div>

            <!-- Price Calculation (Frontend Calculated) -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl shadow border-2 border-blue-300 p-6 mb-6 space-y-4">
                <h2 class="font-bold text-lg">💡 Profit Calculation</h2>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between p-3 bg-white rounded">
                        <span class="font-semibold">Total Gold Value (Today's Rate)</span>
                        <span class="font-bold text-blue-600">₱<span id="totalValue">{{ number_format($receipt->total_item_value, 0) }}</span></span>
                    </div>
                    <div class="flex justify-between p-3 bg-white rounded">
                        <span class="font-semibold">Minus: Pawn Lukat Fee</span>
                        <span class="font-bold text-red-600">-₱<span id="displayLukatFee">{{ number_format($receipt->lukat_fee, 0) }}</span></span>
                    </div>
                </div>

                <div class="border-t-2 pt-4 mt-4">
                    <div class="flex justify-between items-center p-3 bg-gradient-to-r from-green-100 to-emerald-100 rounded-lg border-2 border-green-400">
                        <span class="font-bold text-lg">Offer Range</span>
                        <span class="font-bold text-green-600 text-2xl">₱<span id="offerRange">{{ number_format($receipt->profit_margin, 0) }}</span></span>
                    </div>
                    <p class="text-xs text-gray-600 mt-2">Maximum you can offer without losing money</p>
                </div>
            </div>

            <!-- Offer Section -->
            <div class="bg-white rounded-xl shadow p-6 mb-6 space-y-4">
                <h2 class="font-bold text-lg">🤝 Make Your Offer</h2>
                <p class="text-sm text-gray-600">Decide how much you want to offer the owner:</p>

                @if ($receipt->status === 'pending' || $receipt->status === 'offered')
                <form action="{{ route('receipts.offer', $receipt) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold mb-2">Your Offer Price</label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-xl">₱</span>
                            <input type="number" name="final_buying_price" step="1"
                                value="{{ old('final_buying_price', $receipt->final_buying_price ?? 0) }}"
                                id="offerInput"
                                max="{{ $receipt->profit_margin }}"
                                class="w-full pl-10 pr-4 py-3 text-lg border-2 border-gray-300 rounded-lg focus:border-green-500"
                                placeholder="Enter offer price" required>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Maximum: ₱<span id="maxOffer">{{ number_format($receipt->profit_margin, 0) }}</span></p>
                    </div>

                    <!-- Your Profit Display -->
                    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border-2 border-emerald-300 rounded-lg p-4 space-y-2">
                        <p class="text-sm font-semibold text-gray-700">💰 Your Estimated Profit:</p>
                        <p class="text-3xl font-bold text-emerald-600">₱<span id="profitAmount">0</span></p>
                        <p class="text-xs text-gray-600">Gold Value (₱<span id="goldValue">{{ number_format($receipt->total_item_value, 0) }}</span>) - Lukat (₱<span id="lukatVal">{{ number_format($receipt->lukat_fee, 0) }}</span>) - Offer (₱<span id="offerAmount">0</span>)</p>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white font-bold py-3 rounded-lg hover:from-green-700 hover:to-emerald-700">
                        🤝 Make Offer
                    </button>
                </form>
                @endif

                @if ($receipt->status === 'offered' && $receipt->final_buying_price)
                <div class="p-4 bg-blue-50 rounded-lg border-2 border-blue-300">
                    <p class="text-sm text-gray-600">Current Offer</p>
                    <p class="text-2xl font-bold text-blue-600 mb-3">₱{{ number_format($receipt->final_buying_price, 0) }}</p>

                    <form action="{{ route('receipts.accept', $receipt) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold py-3 rounded-lg hover:from-emerald-700 hover:to-teal-700">
                            ✅ Accept & Complete Purchase
                        </button>
                    </form>
                </div>
                @endif

                @if ($receipt->status === 'completed')
                <div class="p-4 bg-green-100 rounded-lg border-2 border-green-500 space-y-3">
                    <p class="text-green-900 font-bold">✅ Purchase Completed</p>
                    <p class="text-green-800">Final Price: ₱{{ number_format($receipt->final_buying_price, 0) }}</p>

                    <!-- Profit Display -->
                    <div class="bg-white rounded-lg p-3 space-y-1">
                        <p class="text-sm text-gray-600 font-semibold">💰 Your Profit:</p>
                        <p class="text-2xl font-bold text-emerald-600">₱{{ number_format(max(0, $receipt->total_item_value - $receipt->lukat_fee - $receipt->final_buying_price), 0) }}</p>
                        <p class="text-xs text-gray-500">Gold Value (₱{{ number_format($receipt->total_item_value, 0) }}) - Lukat (₱{{ number_format($receipt->lukat_fee, 0) }}) - Offer (₱{{ number_format($receipt->final_buying_price, 0) }})</p>
                    </div>

                    @if (!$receipt->e_bagsak)
                    <form action="{{ route('receipts.e-bagsak', $receipt) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold py-3 rounded-lg hover:from-purple-700 hover:to-pink-700 transition">
                            🎉 Bagsak na!
                        </button>
                    </form>
                    @else
                    <div class="bg-purple-50 border-2 border-purple-400 rounded-lg p-3 text-center">
                        <p class="text-purple-900 font-bold">🎉 Na bagsak na!</p>
                        <p class="text-sm text-purple-800">Forwarded to boss on {{ $receipt->e_bagsak_at->format('M d, Y H:i') }}</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            <!-- Navigation -->
            <div class="flex gap-4 mb-6">
                <a href="/receipts" class="flex-1 text-center bg-gray-300 text-gray-900 font-bold py-3 rounded-lg">
                    ← Back to Receipts
                </a>
                <form action="{{ route('receipts.destroy', $receipt) }}" method="POST" class="flex-1" onsubmit="return confirm('Delete this receipt? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full text-center bg-red-500 hover:bg-red-600 text-white font-bold py-3 rounded-lg">
                        🗑️ Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const totalItemValueOriginal = {{ $receipt->total_item_value }};
        const karatMultipliers = {!! json_encode($karats->pluck('multiplier', 'id')->toArray()) !!};
        const karatValues = {!! json_encode($karats->pluck('karat_value', 'id')->toArray()) !!};
        const todaysGoldRate = {{ \App\Models\GoldPrice::whereDate('date', today())->first()?->daily_price ?? 0 }};
        let isEditMode = false;

        function toggleEditMode() {
            isEditMode = !isEditMode;
            const btn = document.getElementById('editToggleBtn');
            const saveBtn = document.getElementById('saveBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const updateForm = document.getElementById('updateForm');
            const itemsDisplay = document.getElementById('itemsDisplay');
            const itemsEditMode = document.getElementById('itemsEditMode');
            const lukatDisplay = document.getElementById('lukatDisplay');
            const lukatEditMode = document.getElementById('lukatEditMode');

            if (isEditMode) {
                // Switch to edit mode
                itemsDisplay.style.display = 'none';
                itemsEditMode.style.display = 'block';
                lukatDisplay.style.display = 'none';
                lukatEditMode.style.display = 'block';
                btn.style.display = 'none';
                saveBtn.parentElement.style.display = 'inline-block';
                cancelBtn.style.display = 'inline-block';

                updateEditItemsInput();
            } else {
                // Switch back to display mode
                itemsDisplay.style.display = 'block';
                itemsEditMode.style.display = 'none';
                lukatDisplay.style.display = 'flex';
                lukatEditMode.style.display = 'none';
                btn.style.display = 'inline-block';
                saveBtn.parentElement.style.display = 'none';
                cancelBtn.style.display = 'none';

                updateCalculations();
            }
        }

        function cancelEditMode() {
            isEditMode = true;
            toggleEditMode();
        }

        document.getElementById('updateForm').addEventListener('submit', function(e) {
            const items = [];
            document.querySelectorAll('.edit-item-row').forEach(row => {
                const grams = row.querySelector('.edit-grams').value;
                const karatId = row.querySelector('.edit-karat-select').value;
                if (grams && karatId) {
                    items.push({ grams: parseFloat(grams), karat_id: parseInt(karatId) });
                }
            });

            if (items.length === 0) {
                e.preventDefault();
                alert('Please add at least one item');
                return false;
            }

            document.getElementById('updateItemsInput').value = JSON.stringify(items);
            document.getElementById('updateLukatFeeInput').value = document.getElementById('lukatFeeInput').value;
        });

        function addEditItem() {
            const container = document.getElementById('editItemsContainer');
            const karatOptions = Object.entries(karatValues).map(([id, karat]) => `<option value="${id}">${karat}K</option>`).join('');
            const html = `
                <div class="flex gap-2 edit-item-row">
                    <input type="number" step="0.01" placeholder="Grams" class="flex-1 px-3 py-2 border-2 border-gray-200 rounded-lg edit-grams">
                    <select class="w-32 px-3 py-2 border-2 border-gray-200 rounded-lg edit-karat-select">
                        <option value="">Select</option>
                        ${karatOptions}
                    </select>
                    <button type="button" onclick="removeEditItem(this)" class="bg-red-500 text-white px-3 py-2 rounded-lg">✕</button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        }

        function removeEditItem(btn) {
            btn.parentElement.remove();
        }

        function updateEditItemsInput() {
            const items = [];
            document.querySelectorAll('.edit-item-row').forEach(row => {
                const grams = row.querySelector('.edit-grams').value;
                const karatId = row.querySelector('.edit-karat-select').value;
                if (grams && karatId) {
                    items.push({ grams: parseFloat(grams), karat_id: parseInt(karatId) });
                }
            });
            document.getElementById('editItemsInput').value = JSON.stringify(items);
        }

        function calculateItemsValue() {
            let totalValue = 0;
            document.querySelectorAll('.edit-item-row').forEach(row => {
                const grams = parseFloat(row.querySelector('.edit-grams').value) || 0;
                const karatId = parseInt(row.querySelector('.edit-karat-select').value);
                if (karatId && karatMultipliers[karatId]) {
                    const pricePerGram = todaysGoldRate * karatMultipliers[karatId];
                    totalValue += pricePerGram * grams;
                }
            });
            return totalValue;
        }

        function calculateReceiptItemsValue() {
            // Calculate value based on the receipt's actual items (not edit items)
            let totalValue = 0;
            const receiptItems = {!! json_encode($receipt->items) !!};

            if (todaysGoldRate > 0 && receiptItems) {
                receiptItems.forEach(item => {
                    if (karatMultipliers[item.karat_id]) {
                        const pricePerGram = todaysGoldRate * karatMultipliers[item.karat_id];
                        totalValue += pricePerGram * item.grams;
                    }
                });
            }
            return totalValue;
        }

        function updateCalculations() {
            const lukatFee = parseFloat(document.getElementById('lukatFeeInput').value) || 0;
            const totalValue = calculateItemsValue();
            const offerRange = Math.max(0, totalValue - lukatFee);

            // Update display values
            document.getElementById('displayLukatFee').textContent = lukatFee.toLocaleString('en-US', {maximumFractionDigits: 0});
            document.getElementById('totalValue').textContent = totalValue.toLocaleString('en-US', {maximumFractionDigits: 0});
            document.getElementById('offerRange').textContent = offerRange.toLocaleString('en-US', {maximumFractionDigits: 0});
            document.getElementById('maxOffer').textContent = offerRange.toLocaleString('en-US', {maximumFractionDigits: 0});
            document.getElementById('lukatAmount').textContent = lukatFee.toLocaleString('en-US', {maximumFractionDigits: 0});
            document.getElementById('lukatVal').textContent = lukatFee.toLocaleString('en-US', {maximumFractionDigits: 0});
            document.getElementById('goldValue').textContent = totalValue.toLocaleString('en-US', {maximumFractionDigits: 0});
            document.getElementById('offerInput').max = offerRange;

            // Recalculate profit with updated gold value
            if (document.getElementById('offerInput').value) {
                document.getElementById('offerInput').dispatchEvent(new Event('input'));
            }
        }

        // Update on input changes
        document.addEventListener('input', function(e) {
            if (isEditMode && (e.target.classList.contains('edit-grams') || e.target.classList.contains('edit-karat-select'))) {
                updateEditItemsInput();
            }
            if (e.target.id === 'lukatFeeInput') {
                updateCalculations();
            }
        });

        // Initial calculation
        updateCalculations();

        // Update profit when offer price changes
        document.getElementById('offerInput').addEventListener('input', function() {
            // Get current values from display (these update when gold price changes)
            const goldValueText = document.getElementById('goldValue').textContent.replace(/,/g, '') || 0;
            const goldValue = parseFloat(goldValueText) || 0;
            const lukatFee = parseFloat(document.getElementById('lukatVal').textContent.replace(/,/g, '')) || 0;
            const offerPrice = parseFloat(this.value) || 0;
            const profit = goldValue - lukatFee - offerPrice;

            // Format profit with sign
            const profitElement = document.getElementById('profitAmount');
            profitElement.textContent = profit.toLocaleString('en-US', {maximumFractionDigits: 0});

            // Change color if negative
            if (profit < 0) {
                profitElement.parentElement.className = 'text-3xl font-bold text-red-600';
            } else {
                profitElement.parentElement.className = 'text-3xl font-bold text-emerald-600';
            }

            document.getElementById('offerAmount').textContent = offerPrice.toLocaleString('en-US', {maximumFractionDigits: 0});
        });

        // Trigger initial profit calculation
        document.getElementById('offerInput').dispatchEvent(new Event('input'));
    </script>
</body>
</html>


