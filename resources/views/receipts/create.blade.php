<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>New Receipt - Malek & Golds</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; }
        .safe-area-top { padding-top: max(1rem, env(safe-area-inset-top)); }
        .safe-area-bottom { padding-bottom: max(1rem, env(safe-area-inset-bottom)); }
        .safe-area-left { padding-left: max(1rem, env(safe-area-inset-left)); }
        .safe-area-right { padding-right: max(1rem, env(safe-area-inset-right)); }
        input, select { -webkit-appearance: none; }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-indigo-50 safe-area-top safe-area-bottom">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white sticky top-0 z-10 shadow-lg">
            <div class="px-4 safe-area-left safe-area-right py-5">
                <div class="flex items-center justify-between mb-2">
                    <h1 class="text-2xl lg:text-3xl font-bold">💎 New Receipt</h1>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm bg-black/20 hover:bg-black/30 px-3 py-2 rounded-lg">🚪</button>
                    </form>
                </div>
                <p class="text-blue-100 text-sm">Enter gold items and pawn lukat fee</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto px-4 safe-area-left safe-area-right py-6 max-w-2xl mx-auto">
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                    <p class="text-red-800 font-semibold text-sm">⚠️ Errors</p>
                    @foreach ($errors->all() as $error)
                        <p class="text-red-700 text-sm">• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('receipts.store') }}" method="POST" id="receiptForm" class="space-y-6">
                @csrf

                <!-- Receipt Header -->
                <div class="bg-white rounded-xl shadow p-6 space-y-4">
                    <h2 class="font-bold text-lg text-gray-900">Receipt Information</h2>

                    <div class="space-y-3">
                        <input type="text" name="receipt_number" placeholder="Receipt Number" class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500" required>
                        <input type="text" name="owner_name" placeholder="Owner Name" class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500" required>
                        <input type="tel" name="owner_contact" placeholder="Contact (optional)" class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500">
                        <input type="text" name="pawn_shop_name" placeholder="Pawn Shop Name (optional)" class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500">
                        <textarea name="address" placeholder="Address (optional)" class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 resize-none" rows="2"></textarea>
                    </div>
                </div>

                <!-- Gold Items -->
                <div class="bg-white rounded-xl shadow p-6 space-y-4">
                    <h2 class="font-bold text-lg text-gray-900">Gold Items</h2>
                    <p class="text-sm text-gray-600">Example: 3g of 24K, 1g of 18K</p>

                    <div id="itemsContainer" class="space-y-3">
                        <!-- Items added here -->
                    </div>

                    <button type="button" onclick="addItem()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        + Add Item
                    </button>

                    <input type="hidden" name="items" id="itemsInput" required>
                </div>

                <!-- Lukat Fee -->
                <div class="bg-amber-50 rounded-xl shadow border-2 border-amber-300 p-6 space-y-4">
                    <h2 class="font-bold text-lg text-gray-900">Pawn Lukat Fee</h2>
                    <p class="text-sm text-gray-600">Total pawn storage fee to be deducted</p>

                    <div class="flex items-center gap-2">
                        <span class="text-2xl">₱</span>
                        <input type="number" name="lukat_fee" step="1" placeholder="0" class="flex-1 px-4 py-3 text-lg border-2 border-amber-300 rounded-lg focus:border-amber-600" required>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex gap-4 mb-6">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold py-3 rounded-lg">
                        ✨ Evaluate Receipt
                    </button>
                    <a href="/receipts" class="flex-1 bg-gray-300 text-gray-900 font-bold py-3 rounded-lg text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        let itemCount = 0;
        const karats = {!! json_encode($karats->pluck('karat_value', 'id')->toArray()) !!};

        function addItem() {
            const container = document.getElementById('itemsContainer');
            const html = `
                <div class="flex gap-2 item-row" id="item-${itemCount}">
                    <input type="number" step="0.01" placeholder="Grams" class="flex-1 px-3 py-2 border-2 border-gray-200 rounded-lg grams" required>
                    <select class="w-32 px-3 py-2 border-2 border-gray-200 rounded-lg karat-select" required>
                        <option value="">Karat</option>
                        ${Object.entries(karats).map(([id, karat]) => `<option value="${id}">${karat}K</option>`).join('')}
                    </select>
                    <button type="button" onclick="removeItem('item-${itemCount}')" class="bg-red-500 text-white px-3 py-2 rounded-lg">✕</button>
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
