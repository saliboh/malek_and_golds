<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>New Receipt - Malek & Golds</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        .safe-area-top { padding-top: max(1rem, env(safe-area-inset-top)); }
        .safe-area-bottom { padding-bottom: max(1rem, env(safe-area-inset-bottom)); }
        .safe-area-left { padding-left: max(1rem, env(safe-area-inset-left)); }
        .safe-area-right { padding-right: max(1rem, env(safe-area-inset-right)); }
        input, select, textarea { -webkit-appearance: none; }
        .mobile-spacer { height: 80px; }
        @media (min-width: 768px) {
            .mobile-spacer { height: 0; }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-indigo-50 safe-area-top">
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Global Navigation -->
        @include('layouts.navigation')

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col w-full lg:w-auto">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white sticky top-0 z-10 shadow-lg">
                <div class="px-4 safe-area-left safe-area-right py-4 lg:py-5">
                    <div class="flex items-center justify-between mb-1 lg:mb-2">
                        <h1 class="text-2xl lg:text-3xl font-bold">💎 New Receipt</h1>
                    </div>
                    <p class="text-blue-100 text-xs lg:text-sm">Enter gold items and pawn lukat fee</p>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 overflow-y-auto w-full">
                <div class="w-full max-w-2xl mx-auto px-4 safe-area-left safe-area-right py-4 lg:py-6">

                    @if ($errors->any())
                        <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-3 lg:p-4 rounded-r-lg">
                            <p class="text-red-800 font-semibold text-sm">⚠️ Errors</p>
                            @foreach ($errors->all() as $error)
                                <p class="text-red-700 text-xs lg:text-sm">• {{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('receipts.store') }}" method="POST" id="receiptForm" class="space-y-4 lg:space-y-6 pb-8 lg:pb-6">
                        @csrf

                        <!-- Receipt Information Section -->
                        <div class="bg-white rounded-lg lg:rounded-xl shadow-md lg:shadow p-4 lg:p-6 space-y-3 lg:space-y-4">
                            <h2 class="font-bold text-base lg:text-lg text-gray-900 flex items-center gap-2">
                                <span>📝</span> Receipt Information
                            </h2>

                            <div class="space-y-3">
                                <div>
                                    <label class="text-xs font-semibold text-gray-600 uppercase">Receipt Number *</label>
                                    <input type="text" name="receipt_number" placeholder="e.g., RCP-001" class="w-full mt-1 px-3 lg:px-4 py-2 lg:py-2.5 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none text-sm" required>
                                </div>

                                <div>
                                    <label class="text-xs font-semibold text-gray-600 uppercase">Owner Name *</label>
                                    <input type="text" name="owner_name" placeholder="e.g., Juan Dela Cruz" class="w-full mt-1 px-3 lg:px-4 py-2 lg:py-2.5 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none text-sm" required>
                                </div>

                                <div>
                                    <label class="text-xs font-semibold text-gray-600 uppercase">Contact (Optional)</label>
                                    <input type="tel" name="owner_contact" placeholder="e.g., +63 912 345 6789" class="w-full mt-1 px-3 lg:px-4 py-2 lg:py-2.5 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none text-sm">
                                </div>

                                <div>
                                    <label class="text-xs font-semibold text-gray-600 uppercase">Pawn Shop Name (Optional)</label>
                                    <input type="text" name="pawn_shop_name" placeholder="e.g., Malek Pawn" class="w-full mt-1 px-3 lg:px-4 py-2 lg:py-2.5 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none text-sm">
                                </div>

                                <div>
                                    <label class="text-xs font-semibold text-gray-600 uppercase">📍 Address (Optional)</label>
                                    <textarea name="address" placeholder="e.g., 123 Main Street, Downtown" class="w-full mt-1 px-3 lg:px-4 py-2 lg:py-2.5 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none resize-none text-sm" rows="2"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Gold Items Section -->
                        <div class="bg-white rounded-lg lg:rounded-xl shadow-md lg:shadow p-4 lg:p-6 space-y-3 lg:space-y-4">
                            <h2 class="font-bold text-base lg:text-lg text-gray-900 flex items-center gap-2">
                                <span>💰</span> Gold Items
                            </h2>
                            <p class="text-xs lg:text-sm text-gray-600">Example: 3g of 24K, 1g of 18K</p>

                            <div id="itemsContainer" class="space-y-2 max-h-96 overflow-y-auto">
                                <!-- Items added here -->
                            </div>

                            <button type="button" onclick="addItem()" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-3 lg:px-4 py-2 lg:py-2.5 rounded-lg font-semibold text-sm lg:text-base transition">
                                + Add Item
                            </button>

                            <input type="hidden" name="items" id="itemsInput" required>
                        </div>

                        <!-- Lukat Fee Section -->
                        <div class="bg-amber-50 rounded-lg lg:rounded-xl shadow-md lg:shadow border-2 border-amber-300 p-4 lg:p-6 space-y-3 lg:space-y-4">
                            <h2 class="font-bold text-base lg:text-lg text-gray-900">Pawn Lukat Fee *</h2>
                            <p class="text-xs lg:text-sm text-gray-600">Total pawn storage fee to be deducted</p>

                            <div class="flex items-center gap-2 bg-white rounded-lg p-3">
                                <span class="text-xl lg:text-2xl font-bold text-amber-600">₱</span>
                                <input type="number" name="lukat_fee" step="1" placeholder="0" class="flex-1 px-2 lg:px-3 py-2 lg:py-2.5 border-0 focus:outline-none text-lg lg:text-xl font-semibold" required>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex gap-3 pb-4">
                            <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold py-3 lg:py-3.5 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition text-sm lg:text-base">
                                ✨ Evaluate Receipt
                            </button>
                            <a href="/receipts" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-900 font-bold py-3 lg:py-3.5 rounded-lg text-center transition text-sm lg:text-base">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile spacer for bottom navigation -->
    <div class="mobile-spacer"></div>

    <script>
        let itemCount = 0;
        const karats = {!! json_encode($karats->pluck('karat_value', 'id')->toArray()) !!};

        function addItem() {
            const container = document.getElementById('itemsContainer');
            const html = `
                <div class="flex flex-col sm:flex-row gap-2 item-row p-3 bg-blue-50 rounded-lg border border-blue-200" id="item-${itemCount}">
                    <input type="number" step="0.01" placeholder="Grams" class="flex-1 px-3 py-2 border-2 border-gray-200 rounded-lg grams focus:border-blue-500 focus:outline-none text-sm" required>
                    <select class="sm:w-40 px-3 py-2 border-2 border-gray-200 rounded-lg karat-select focus:border-blue-500 focus:outline-none text-sm" required>
                        <option value="">Select Karat</option>
                        ${Object.entries(karats).map(([id, karat]) => `<option value="${id}">${karat}K</option>`).join('')}
                    </select>
                    <button type="button" onclick="removeItem('item-${itemCount}')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg font-semibold text-sm transition">✕</button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            itemCount++;
            updateItems();
        }

        function removeItem(id) {
            document.getElementById(id).remove();
            updateItems();
        }

        function updateItems() {
            const items = [];
            document.querySelectorAll('.item-row').forEach(row => {
                const grams = row.querySelector('.grams').value;
                const karatId = row.querySelector('.karat-select').value;
                if (grams && karatId) {
                    items.push({ grams: parseFloat(grams), karat_id: parseInt(karatId) });
                }
            });
            document.getElementById('itemsInput').value = JSON.stringify(items);
        }

        document.addEventListener('input', updateItems);
        addItem();
    </script>
</body>
</html>
</html>
