<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Receipt - Malek & Golds</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; }
        .safe-area { padding: max(1rem, env(safe-area-inset-top)) max(1rem, env(safe-area-inset-right)) max(1rem, env(safe-area-inset-bottom)) max(1rem, env(safe-area-inset-left)); }
        @media print { .no-print { display: none; } body { background: white; } }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white sticky top-0 z-10 shadow-lg">
            <div class="px-4 py-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold">💎 Malek & Golds</h1>
                    <button onclick="window.print()" class="no-print bg-white/20 hover:bg-white/30 text-white px-3 py-2 rounded-lg text-sm font-semibold">
                        🖨️ Print
                    </button>
                </div>
                <p class="text-indigo-100 text-sm">Receipt Offer Evaluation</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto px-4 py-6 max-w-2xl mx-auto w-full">

            <!-- MAIN INFO CARD - Owner, Address, Pawnshop -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl shadow-xl p-8 mb-6 border-2 border-indigo-200">
                <div class="space-y-5">
                    <!-- Owner Name - Prominent -->
                    <div>
                        <p class="text-xs text-indigo-600 font-bold uppercase tracking-widest mb-3">👤 Owner Name</p>
                        <p class="text-4xl font-black text-indigo-900">{{ $receipt->owner_name ?? 'N/A' }}</p>
                    </div>

                    <!-- Pawn Shop -->
                    @if ($receipt->pawn_shop_name)
                    <div class="border-l-4 border-indigo-500 pl-4 py-2">
                        <p class="text-xs text-indigo-600 font-bold uppercase tracking-widest">🏪 Pawn Shop</p>
                        <p class="text-xl font-bold text-gray-900">{{ $receipt->pawn_shop_name }}</p>
                    </div>
                    @endif

                    <!-- Address -->
                    @if ($receipt->address)
                    <div class="bg-white rounded-lg p-4 border-l-4 border-amber-500">
                        <p class="text-xs text-amber-700 font-bold uppercase tracking-widest mb-2">📍 Address</p>
                        <p class="text-lg text-gray-900 leading-relaxed font-semibold">{{ $receipt->address }}</p>
                    </div>
                    @endif

                    <!-- Receipt # and Date -->
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t-2 border-indigo-200">
                        <div>
                            <p class="text-xs text-indigo-600 font-bold uppercase tracking-widest">Receipt #</p>
                            <p class="text-lg font-bold text-indigo-700">{{ $receipt->receipt_number }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-indigo-600 font-bold uppercase tracking-widest">Date</p>
                            <p class="text-lg font-bold text-gray-900">{{ $receipt->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- OFFER RANGE - Prominently Displayed -->
            @if ($receipt->status === 'pending' || $receipt->status === 'offered')
            @php
                $offerRange = $receipt->profit_margin;
                $isNegative = $offerRange < 0;
                $currentOffer = $receipt->final_buying_price ?? 0;
                $profitAtCurrentOffer = $receipt->total_item_value - $receipt->lukat_fee - $currentOffer;
                $hasNegativeProfit = $profitAtCurrentOffer < 0;
            @endphp
            <div class="bg-gradient-to-r {{ $isNegative ? 'from-red-50 to-rose-50' : 'from-green-50 to-emerald-50' }} rounded-2xl shadow-xl p-8 mb-6 border-4 {{ $isNegative ? 'border-red-400' : 'border-green-400' }}">
                <div class="text-center">
                    <p class="text-sm {{ $isNegative ? 'text-red-700' : 'text-green-700' }} font-bold uppercase tracking-widest mb-3">💰 Offer Range</p>
                    <p class="text-5xl font-black {{ $isNegative ? 'text-red-600' : 'text-green-600' }} mb-2">₱{{ number_format($offerRange, 0) }}</p>
                    <p class="text-sm text-gray-700 mb-3">{{ $isNegative ? 'Loss - Lukat fee exceeds gold value' : 'Maximum you can offer without losing money' }}</p>

                    @if ($hasNegativeProfit)
                    <div class="bg-red-100 border-2 border-red-400 rounded-lg p-3 mt-4">
                        <p class="text-sm font-bold text-red-700">⚠️ WARNING: Negative Profit</p>
                        <p class="text-xs text-red-600 mt-1">This transaction will result in a loss of ₱{{ number_format(abs($profitAtCurrentOffer), 0) }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @elseif ($receipt->status === 'completed')
            <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-2xl shadow-xl p-8 mb-6 border-4 border-yellow-400">
                <div class="text-center">
                    <p class="text-sm text-yellow-700 font-bold uppercase tracking-widest mb-3">💰 Your Profit</p>
                    <p class="text-5xl font-black text-yellow-600 mb-2">₱{{ number_format(max(0, $receipt->total_item_value - $receipt->lukat_fee - $receipt->final_buying_price), 0) }}</p>
                    <p class="text-sm text-gray-700">Gold Value - Lukat - Purchase Price</p>
                </div>
            </div>
            @endif

            <!-- Gold Items Breakdown -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">💰 Gold Items Breakdown</h2>
                <div class="space-y-3">
                    @foreach ($breakdown as $index => $item)
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 border-l-4 border-blue-500">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="text-xs font-semibold text-blue-600 uppercase">Item {{ $index + 1 }}</p>
                                <p class="text-lg font-bold text-gray-900">{{ number_format($item['grams'], 2) }}g of {{ $item['karat'] }}K Gold</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-600">Price per gram</p>
                                <p class="text-lg font-bold text-blue-600">₱{{ number_format($item['price_per_gram'], 0) }}</p>
                            </div>
                        </div>
                        <div class="bg-white rounded px-3 py-2">
                            <p class="text-xs text-gray-600 mb-1">Total for this item</p>
                            <p class="text-2xl font-bold text-blue-600">₱{{ number_format($item['total'], 0) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Calculation Summary -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">📊 Calculation Summary</h2>

                <div class="space-y-3">
                    <!-- Today's Gold Base Rate -->
                    @if ($todayGoldPrice)
                    <div class="flex justify-between items-center p-4 bg-indigo-50 rounded-lg border-l-4 border-indigo-500">
                        <div>
                            <p class="text-sm text-gray-700 font-semibold">Today's Gold Base Rate</p>
                            <p class="text-xs text-gray-600">Market price per gram</p>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-indigo-600">₱{{ number_format($todayGoldPrice->daily_price, 0) }}/g</p>
                        </div>
                    </div>
                    @endif

                    <!-- Total Gold Value -->
                    <div class="flex justify-between items-center p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                        <div>
                            <p class="text-sm text-gray-700 font-semibold">Total Gold Value of Items</p>
                            <p class="text-xs text-gray-600">Based on today's market rate</p>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-blue-600">₱{{ number_format($receipt->total_item_value, 0) }}</p>
                        </div>
                    </div>

                    <!-- Lukat Fee -->
                    <div class="flex justify-between items-center p-4 bg-amber-50 rounded-lg border-l-4 border-amber-500">
                        <div>
                            <p class="text-sm text-gray-700 font-semibold">Pawn Lukat Fee</p>
                            <p class="text-xs text-gray-600">Storage & handling charge</p>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-amber-600">-₱{{ number_format($receipt->lukat_fee, 0) }}</p>
                        </div>
                    </div>

                    <div class="border-t-2 border-gray-200 my-2"></div>

                    <!-- Status-Based Details (Additional Info) -->
                    @if ($receipt->status === 'pending' || $receipt->status === 'offered')
                        <!-- Profit Based on Current or Zero Offer -->
                        @php
                            $currentOffer = $receipt->final_buying_price ?? 0;
                            $profitAtCurrentOffer = $receipt->total_item_value - $receipt->lukat_fee - $currentOffer;
                        @endphp
                        <div class="flex justify-between items-center p-4 bg-orange-50 rounded-lg border-l-4 border-orange-500">
                            <div>
                                <p class="text-sm text-gray-700 font-semibold">Profit at Current Offer</p>
                                <p class="text-xs text-gray-600">Gold Value - Lukat - Your Offer (or ₱0)</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold {{ $profitAtCurrentOffer < 0 ? 'text-red-600' : 'text-orange-600' }}">₱{{ number_format($profitAtCurrentOffer, 0) }}</p>
                            </div>
                        </div>

                        @if ($receipt->final_buying_price)
                        <div class="flex justify-between items-center p-4 bg-purple-50 rounded-lg border-l-4 border-purple-500">
                            <div>
                                <p class="text-sm text-gray-700 font-semibold">Your Current Offer</p>
                                <p class="text-xs text-gray-600">Amount offered to owner</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-purple-600">₱{{ number_format($receipt->final_buying_price, 0) }}</p>
                            </div>
                        </div>

                        <div class="flex justify-between items-center p-4 bg-rose-50 rounded-lg border-l-4 border-rose-500">
                            <div>
                                <p class="text-sm text-gray-700 font-semibold">Your Profit</p>
                                <p class="text-xs text-gray-600">Gold Value - Lukat - Offer</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-rose-600">₱{{ number_format(max(0, $receipt->total_item_value - $receipt->lukat_fee - $receipt->final_buying_price), 0) }}</p>
                            </div>
                        </div>
                        @endif

                    @elseif ($receipt->status === 'completed')
                        <div class="flex justify-between items-center p-4 bg-green-50 rounded-lg border-l-4 border-green-500">
                            <div>
                                <p class="text-sm text-gray-700 font-semibold">✅ Final Purchase Price</p>
                                <p class="text-xs text-gray-600">Agreed price with owner</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-green-600">₱{{ number_format($receipt->final_buying_price, 0) }}</p>
                            </div>
                        </div>

                        <div class="flex justify-between items-center p-4 bg-yellow-100 rounded-lg border-2 border-yellow-400 shadow-md">
                            <div>
                                <p class="text-sm text-gray-800 font-bold">💰 YOUR PROFIT</p>
                                <p class="text-xs text-gray-700">Gold Value - Lukat - Purchase Price</p>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold text-yellow-600">₱{{ number_format(max(0, $receipt->total_item_value - $receipt->lukat_fee - $receipt->final_buying_price), 0) }}</p>
                            </div>
                        </div>

                        @if ($receipt->e_bagsak)
                        <div class="flex items-center gap-3 p-4 bg-gradient-to-r from-purple-50 to-pink-100 rounded-lg border-l-4 border-purple-500">
                            <span class="text-3xl">🎉</span>
                            <div>
                                <p class="text-sm text-gray-800 font-bold">Gold Forwarded to Boss</p>
                                <p class="text-xs text-gray-700">{{ $receipt->e_bagsak_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Footer Info -->
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-6 mb-6 border border-indigo-200">
                <p class="text-xs text-gray-600 text-center mb-3">
                    <strong>Malek & Golds</strong><br>
                    This is a readonly receipt offer evaluation document.
                </p>
                <p class="text-xs text-gray-500 text-center">
                    Generated on {{ now()->format('F d, Y \a\t g:i A') }}
                </p>
            </div>

            <!-- Print/Share Buttons -->
            <div class="flex gap-3 mb-6 no-print">
                <button onclick="window.print()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition shadow-md">
                    🖨️ Print
                </button>
                <button onclick="copyToClipboard()" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg transition shadow-md">
                    📋 Copy URL
                </button>
                <button onclick="window.close()" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 rounded-lg transition shadow-md">
                    ❌ Close
                </button>
            </div>

        </div>
    </div>

    <script>
        function copyToClipboard() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                alert('Receipt URL copied to clipboard!\nYou can now share this link with your coworker.');
            }).catch(() => {
                alert('Failed to copy. Please try again.');
            });
        }
    </script>
</body>
</html>
