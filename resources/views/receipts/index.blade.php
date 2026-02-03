<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Receipts - Malek & Golds</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; }
        .safe-area-top { padding-top: max(1rem, env(safe-area-inset-top)); }
        .safe-area-bottom { padding-bottom: max(1rem, env(safe-area-inset-bottom)); }
        .safe-area-left { padding-left: max(1rem, env(safe-area-inset-left)); }
        .safe-area-right { padding-right: max(1rem, env(safe-area-inset-right)); }
    </style>
</head>
<body class="bg-gradient-to-br from-purple-50 via-white to-pink-50 safe-area-top safe-area-bottom">
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Global Navigation -->
        @include('layouts.navigation')

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 text-white sticky top-0 z-10 shadow-lg">
            <div class="px-4 safe-area-left safe-area-right py-5">
                <div class="flex items-center justify-between mb-2">
                    <h1 class="text-2xl lg:text-3xl font-bold">💎 Receipts</h1>
                    <span class="text-sm text-purple-100">Manage pawned gold</span>
                </div>
                <p class="text-purple-100 text-sm">Manage pawned gold receipts</p>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="border-b border-gray-300 bg-white sticky top-16 z-5">
            <div class="px-4 safe-area-left safe-area-right max-w-4xl mx-auto flex gap-0">
                <button onclick="switchTab('active')" id="tab-active" class="px-4 py-3 font-semibold text-purple-600 border-b-4 border-purple-600 cursor-pointer">
                    📋 Active (<span id="active-count">0</span>)
                </button>
                <button onclick="switchTab('accepted')" id="tab-accepted" class="px-4 py-3 font-semibold text-gray-600 border-b-2 border-gray-300 hover:text-purple-600 cursor-pointer">
                    ✅ Accepted (<span id="accepted-count">0</span>)
                </button>
                <button onclick="switchTab('bagsak')" id="tab-bagsak" class="px-4 py-3 font-semibold text-gray-600 border-b-2 border-gray-300 hover:text-purple-600 cursor-pointer">
                    🎉 E Bagsak (<span id="bagsak-count">0</span>)
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto px-4 safe-area-left safe-area-right py-6 max-w-4xl mx-auto w-full">

            <!-- Profit Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <!-- Projected Profit Card -->
                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl shadow-lg border-2 border-blue-300 p-6 space-y-2">
                    <p class="text-sm font-semibold text-gray-700">📊 Projected Profit</p>
                    <p class="text-3xl font-bold text-blue-600">₱{{ number_format($projectedProfit, 0) }}</p>
                    <p class="text-xs text-gray-600">From {{ $acceptedReceipts->total() }} accepted offer(s) ready to forward</p>
                </div>

                <!-- Actual Profit Card -->
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl shadow-lg border-2 border-emerald-300 p-6 space-y-2">
                    <p class="text-sm font-semibold text-gray-700">💰 Actual Profit</p>
                    <p class="text-3xl font-bold text-emerald-600">₱{{ number_format($actualProfit, 0) }}</p>
                    <p class="text-xs text-gray-600">From {{ $bagsaksReceipts->total() }} completed transaction(s)</p>
                </div>
            </div>

            <!-- New Receipt Button -->
            <div class="mb-6">
                <a href="{{ route('receipts.create') }}" class="inline-block bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold px-6 py-3 rounded-lg hover:from-purple-700 hover:to-pink-700">
                    + New Receipt
                </a>
            </div>

            <!-- Active Tab -->
            <div id="content-active" class="tab-content">
                @if ($activeReceipts->count() > 0)
                    <div class="space-y-4">
                        @foreach ($activeReceipts as $receipt)
                        <div class="bg-white rounded-xl shadow hover:shadow-lg transition p-4 space-y-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-xs text-gray-600 font-semibold">RECEIPT #</p>
                                    <p class="font-bold text-gray-900">{{ $receipt->receipt_number }}</p>
                                </div>
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold
                                    @if ($receipt->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif ($receipt->status === 'offered') bg-blue-100 text-blue-800
                                    @endif
                                ">
                                    {{ ucfirst($receipt->status) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-600">Owner</p>
                                    <p class="font-bold text-gray-900">{{ $receipt->owner_name }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Offer Range</p>
                                    <p class="font-bold text-purple-600">₱{{ number_format($receipt->profit_margin, 0) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Lukat Fee</p>
                                    <p class="font-bold">₱{{ number_format($receipt->lukat_fee, 0) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Gold Items</p>
                                    <p class="font-bold">{{ count($receipt->items) }} item(s)</p>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ route('receipts.show', $receipt) }}" class="flex-1 text-center bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold py-2 rounded-lg hover:from-purple-700 hover:to-pink-700">
                                    View Details →
                                </a>
                                <form action="{{ route('receipts.destroy', $receipt) }}" method="POST" class="inline" onsubmit="return confirm('Delete this receipt?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold px-4 py-2 rounded-lg">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    {{ $activeReceipts->links('pagination::tailwind') }}
                @else
                    <div class="bg-white rounded-xl shadow p-12 text-center">
                        <p class="text-4xl mb-4">📭</p>
                        <p class="text-xl font-bold text-gray-900 mb-2">No Active Receipts</p>
                        <p class="text-gray-600 mb-6">All receipts have been accepted or completed</p>
                        <a href="{{ route('receipts.create') }}" class="inline-block bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold py-3 px-6 rounded-lg hover:from-purple-700 hover:to-pink-700">
                            + New Receipt
                        </a>
                    </div>
                @endif
            </div>

            <!-- Accepted Tab -->
            <div id="content-accepted" class="tab-content" style="display: none;">
                @if ($acceptedReceipts->count() > 0)
                    <div class="space-y-4">
                        @foreach ($acceptedReceipts as $receipt)
                        <div class="bg-white rounded-xl shadow hover:shadow-lg transition p-4 space-y-3 border-l-4 border-green-500">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-xs text-gray-600 font-semibold">RECEIPT #</p>
                                    <p class="font-bold text-gray-900">{{ $receipt->receipt_number }}</p>
                                </div>
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                    ✅ Completed
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-600">Owner</p>
                                    <p class="font-bold text-gray-900">{{ $receipt->owner_name }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Your Profit</p>
                                    <p class="font-bold text-emerald-600">₱{{ number_format(max(0, $receipt->total_item_value - $receipt->lukat_fee - $receipt->final_buying_price), 0) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Gold Value</p>
                                    <p class="font-bold">₱{{ number_format($receipt->total_item_value, 0) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Your Offer</p>
                                    <p class="font-bold">₱{{ number_format($receipt->final_buying_price, 0) }}</p>
                                </div>
                            </div>

                            <a href="{{ route('receipts.show', $receipt) }}" class="block text-center bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold py-2 rounded-lg hover:from-emerald-700 hover:to-teal-700">
                                Send to bagsakan →
                            </a>
                        </div>
                        @endforeach
                    </div>
                    {{ $acceptedReceipts->links('pagination::tailwind') }}
                @else
                    <div class="bg-white rounded-xl shadow p-12 text-center">
                        <p class="text-4xl mb-4">🎁</p>
                        <p class="text-xl font-bold text-gray-900 mb-2">No Accepted Receipts</p>
                        <p class="text-gray-600">Accepted receipts will appear here</p>
                    </div>
                @endif
            </div>

            <!-- E Bagsak Tab -->
            <div id="content-bagsak" class="tab-content" style="display: none;">
                @if ($bagsaksReceipts->count() > 0)
                    <div class="space-y-4">
                        @foreach ($bagsaksReceipts as $receipt)
                        <div class="bg-white rounded-xl shadow hover:shadow-lg transition p-4 space-y-3 border-l-4 border-purple-500">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-xs text-gray-600 font-semibold">RECEIPT #</p>
                                    <p class="font-bold text-gray-900">{{ $receipt->receipt_number }}</p>
                                </div>
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800">
                                    🎉 E BAGSAK
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-600">Owner</p>
                                    <p class="font-bold text-gray-900">{{ $receipt->owner_name }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Your Profit</p>
                                    <p class="font-bold text-emerald-600">₱{{ number_format(max(0, $receipt->total_item_value - $receipt->lukat_fee - $receipt->final_buying_price), 0) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Gold Value</p>
                                    <p class="font-bold">₱{{ number_format($receipt->total_item_value, 0) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Forwarded Date</p>
                                    <p class="font-bold">{{ $receipt->e_bagsak_at->format('M d, Y') }}</p>
                                </div>
                            </div>

                            <a href="{{ route('receipts.show', $receipt) }}" class="block text-center bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 rounded-lg">
                                View Details →
                            </a>
                        </div>
                        @endforeach
                    </div>
                    {{ $bagsaksReceipts->links('pagination::tailwind') }}
                @else
                    <div class="bg-white rounded-xl shadow p-12 text-center">
                        <p class="text-4xl mb-4">🎊</p>
                        <p class="text-xl font-bold text-gray-900 mb-2">No E Bagsak Receipts Yet</p>
                        <p class="text-gray-600">When receipts are marked as E bagsak, they'll appear here</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Footer Navigation -->
    <div class="safe-area-bottom border-t border-gray-200 bg-white/80 backdrop-blur-sm px-4 py-4 sticky bottom-0">
        <div class="flex items-center justify-between gap-2">
            <a href="/" class="flex-1 py-3 bg-gray-200 text-gray-800 font-bold rounded-lg text-center text-sm">
                🏠 Home
            </a>
            <a href="{{ route('receipts.create') }}" class="flex-1 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-lg text-center text-sm">
                + New
            </a>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.style.display = 'none';
            });
            document.querySelectorAll('[id^="tab-"]').forEach(btn => {
                btn.className = 'px-4 py-3 font-semibold text-gray-600 border-b-2 border-gray-300 hover:text-purple-600 cursor-pointer';
            });

            // Show selected tab
            document.getElementById('content-' + tabName).style.display = 'block';
            const activeBtn = document.getElementById('tab-' + tabName);
            activeBtn.className = 'px-4 py-3 font-semibold text-purple-600 border-b-4 border-purple-600 cursor-pointer';
        }

        // Update counts on load
        document.addEventListener('DOMContentLoaded', function() {
            const activeCount = document.querySelectorAll('#content-active .bg-white.rounded-xl').length;
            const acceptedCount = document.querySelectorAll('#content-accepted .bg-white.rounded-xl').length;
            const bagsaksCount = document.querySelectorAll('#content-bagsak .bg-white.rounded-xl').length;

            document.getElementById('active-count').textContent = activeCount;
            document.getElementById('accepted-count').textContent = acceptedCount;
            document.getElementById('bagsak-count').textContent = bagsaksCount;
        });
    </script>
</body>
</html>
