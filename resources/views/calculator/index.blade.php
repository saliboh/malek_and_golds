<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Malek & Golds</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        .safe-area-top {
            padding-top: max(1rem, env(safe-area-inset-top));
        }
        .safe-area-bottom {
            padding-bottom: max(1rem, env(safe-area-inset-bottom));
        }
        .safe-area-left {
            padding-left: max(1rem, env(safe-area-inset-left));
        }
        .safe-area-right {
            padding-right: max(1rem, env(safe-area-inset-right));
        }
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type="number"] {
            -moz-appearance: textfield;
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        .price-card {
            transition: all 0.3s ease;
        }
        .price-card:active {
            transform: scale(0.98);
        }
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.1), 0 0 0 1px rgba(251, 146, 60, 0.5);
        }
        .sidebar-scroll {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }
        .karat-item {
            padding: 0.875rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }
        .karat-item:last-child {
            border-bottom: none;
        }
        .karat-item:hover {
            background: rgba(251, 146, 60, 0.08);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-yellow-50 via-white to-amber-50 safe-area-top safe-area-bottom">
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Global Navigation -->
        @include('layouts.navigation')

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <div class="bg-gradient-to-r from-amber-600 to-yellow-500 text-white sticky top-0 z-10 shadow-lg">
                <div class="px-4 safe-area-left safe-area-right py-5">
                <div class="flex items-center justify-between mb-3">
                    <h1 class="text-2xl lg:text-3xl font-bold tracking-tight">💎 Malek & Golds</h1>
                    <span class="text-sm text-amber-100">Calculator</span>
                </div>
                    <p class="text-amber-100 text-sm">Gold Price Calculator - Fast & Accurate Pricing</p>
                </div>

                @if ($todayPrice)
                    <div class="px-4 safe-area-left safe-area-right py-3 bg-black/20 backdrop-blur-sm border-t border-white/20">
                        <div class="flex items-center justify-between">
                            <span class="text-amber-100 text-sm font-semibold">📈 Market Rate Today</span>
                            <span class="text-2xl font-bold">₱{{ number_format($todayPrice->daily_price, 0) }}/g</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Main Content -->
            <div class="flex-1 overflow-y-auto">
                <div class="px-4 safe-area-left safe-area-right py-6 max-w-2xl mx-auto">
            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                    <p class="text-red-800 font-semibold text-sm">⚠️ Error</p>
                    @foreach ($errors->all() as $error)
                        <p class="text-red-700 text-sm mt-1">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if (!$todayPrice)
                <div class="mb-6 bg-yellow-50 border-2 border-yellow-400 p-4 rounded-2xl">
                    <p class="text-yellow-800 font-semibold">⚡ Daily Price Not Set</p>
                    <p class="text-yellow-700 text-sm mt-1">Please contact admin to set today's gold price</p>
                </div>
            @endif

            <!-- Input Form Card -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <span class="text-2xl">✨</span> Calculate Your Gold Price
                </h2>

                <form action="/calculate" method="POST" class="space-y-5">
                    @csrf

                    <!-- Grams Input -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-800 ml-1">
                            <span>⚖️</span> Weight in Grams
                        </label>
                        <input
                            type="number"
                            name="grams"
                            step="0.01"
                            placeholder="Enter weight"
                            class="input-focus w-full px-4 py-3 text-lg bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-amber-500 transition placeholder-gray-400"
                            required
                        >
                        <p class="text-xs text-gray-500 ml-1">e.g., 5, 10.5, 25</p>
                    </div>

                    <!-- Karat Selection -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-800 ml-1">
                            <span>👑</span> Gold Purity (Karat)
                        </label>
                        <select name="karat_id" class="input-focus w-full px-4 py-3 text-base bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-amber-500 transition appearance-none cursor-pointer" required>
                            <option value="">Select karat purity...</option>
                            @foreach ($karats as $karat)
                                <option value="{{ $karat->id }}">
                                    {{ $karat->karat_value }}K - {{ $karat->description }} ({{ ($karat->multiplier * 100) }}%)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Calculate Button -->
                    @if ($todayPrice)
                        <button
                            type="submit"
                            class="w-full bg-gradient-to-r from-amber-600 to-yellow-500 hover:from-amber-700 hover:to-yellow-600 text-white font-bold py-4 px-4 rounded-xl transition transform active:scale-95 shadow-lg text-lg mt-6"
                        >
                            💰 Calculate Price
                        </button>
                    @else
                        <button
                            type="button"
                            disabled
                            class="w-full bg-gray-300 text-gray-500 font-bold py-4 px-4 rounded-xl cursor-not-allowed text-lg mt-6"
                        >
                            Set Daily Price First
                        </button>
                    @endif
                </form>
            </div>

            <!-- Results Section -->
            @if (session('pricePerGram'))
                <div class="space-y-4 mb-6">
                    <!-- Market Rate Box -->
                    <div class="glass-effect border-2 border-yellow-300 rounded-2xl p-5 shadow-lg">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="text-2xl">👑</span> Your Calculation
                        </h3>

                        <div class="space-y-3">
                            <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                                <span class="text-gray-600 font-medium">Karat Purity</span>
                                <span class="text-2xl font-bold text-amber-600">{{ session('karatValue') }}K</span>
                            </div>
                            <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                                <span class="text-gray-600 font-medium">Weight</span>
                                <span class="text-xl font-bold text-amber-600">{{ number_format(session('grams'), 2) }}g</span>
                            </div>
                            <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                                <span class="text-gray-600 font-medium">Price Per Gram</span>
                                <span class="text-xl font-bold text-amber-600">₱{{ number_format(session('pricePerGram'), 0) }}</span>
                            </div>
                            <div class="flex justify-between items-center pt-3 bg-yellow-50 p-3 rounded-lg">
                                <span class="text-gray-800 font-bold text-lg">Boss's Price</span>
                                <span class="text-3xl font-bold text-amber-700">₱{{ number_format(session('totalPrice'), 0) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Buying Offers -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                            <span class="text-2xl">💵</span> Our Buying Price Options
                        </h3>
                        <p class="text-xs text-gray-500 mb-4 ml-1">Choose your preferred buying price (Your profit is shown below each option)</p>

                        <div class="space-y-3">
                            @foreach (session('offers') as $index => $offer)
                                <div class="price-card bg-gradient-to-r from-blue-50 to-blue-100 border-2 border-blue-400 rounded-xl overflow-hidden shadow-md hover:shadow-lg">
                                    <!-- Top Section: Price Details -->
                                    <div class="p-4 space-y-3">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 text-white text-sm font-bold">
                                                    {{ $index + 1 }}
                                                </span>
                                                <div>
                                                    <p class="text-xs text-gray-600 font-semibold">DISCOUNT FROM BOSS'S PRICE</p>
                                                    <p class="text-lg font-bold text-blue-900">-₱{{ number_format($offer['margin'], 0) }} per gram</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Price Grid -->
                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="bg-white/90 rounded-lg p-3">
                                                <p class="text-xs text-gray-600 font-semibold mb-1">OFFER PRICE/G</p>
                                                <p class="text-xl font-bold text-blue-600">₱{{ number_format($offer['price_per_gram'], 0) }}</p>
                                            </div>
                                            <div class="bg-white/90 rounded-lg p-3">
                                                <p class="text-xs text-gray-600 font-semibold mb-1">OFFER TOTAL</p>
                                                <p class="text-xl font-bold text-blue-600">₱{{ number_format($offer['total_price'], 0) }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bottom Section: Your Profit (Highlighted) -->
                                    <div class="bg-gradient-to-r from-green-400 to-emerald-500 text-white px-4 py-3 border-t-2 border-green-300">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <span class="text-2xl">💰</span>
                                                <div>
                                                    <p class="text-xs font-semibold opacity-90">YOUR PROFIT</p>
                                                    <p class="text-xs opacity-90">₱{{ number_format($offer['margin'], 0) }} × {{ number_format(session('grams'), 2) }}g</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-2xl font-bold">₱{{ number_format($offer['total_profit'], 0) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Clear & Recalculate -->
                    <div class="text-center pt-4">
                        <a href="/" class="inline-block text-blue-600 hover:text-blue-800 font-semibold text-sm bg-blue-50 px-4 py-2 rounded-lg">↻ Calculate Again</a>
                    </div>
                </div>
            @endif
                </div>
            </div>
        </div>

        <!-- Right Sidebar - Karat Price Reference (Hidden on mobile, visible on lg+) -->
        @if ($todayPrice)
            <div class="hidden lg:flex lg:flex-col lg:w-80 bg-gradient-to-b from-amber-50 to-white border-l border-gray-200">
                <!-- Sidebar Header -->
                <div class="bg-gradient-to-r from-amber-600 to-yellow-500 text-white px-6 py-5 shadow-md">
                    <h3 class="text-lg font-bold">📋 Karat Reference</h3>
                    <p class="text-amber-100 text-sm mt-1">All prices per gram</p>
                </div>

                <!-- Sidebar Scroll Content -->
                <div class="sidebar-scroll px-4 py-4">
                    <div class="space-y-1">
                        @foreach ($karats as $karat)
                            <div class="karat-item hover:bg-yellow-100 rounded-lg cursor-pointer">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-bold text-gray-900">{{ $karat->karat_value }}K</span>
                                    <span class="text-xs text-gray-600">{{ ($karat->multiplier * 100) }}%</span>
                                </div>
                                <div class="flex items-baseline justify-between">
                                    <span class="text-gray-600 text-sm">{{ $karat->description }}</span>
                                    <span class="text-lg font-bold text-amber-600">
                                        ₱{{ number_format($todayPrice->daily_price * $karat->multiplier, 0) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Sidebar Footer -->
                <div class="border-t border-gray-200 p-4 bg-blue-50">
                    <a href="/admin" class="block w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold py-2 px-4 rounded-lg text-center transition hover:from-blue-700 hover:to-indigo-700">
                        ⚙️ Update Daily Rate
                    </a>
                </div>
            </div>
        @endif

        <!-- Mobile Karat Reference (Only visible on small screens) -->
        @if ($todayPrice && session('pricePerGram'))
            <div class="lg:hidden mt-6 px-4 safe-area-left safe-area-right max-w-2xl mx-auto w-full mb-6">
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-amber-600 to-yellow-500 text-white px-4 py-4">
                        <h3 class="text-lg font-bold">📋 Quick Karat Reference</h3>
                    </div>
                    <div class="max-h-64 overflow-y-auto divide-y divide-gray-200">
                        @foreach ($karats as $karat)
                            <div class="karat-item px-4 py-3 hover:bg-yellow-50">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $karat->karat_value }}K - {{ $karat->description }}</p>
                                        <p class="text-xs text-gray-600">{{ ($karat->multiplier * 100) }}% purity</p>
                                    </div>
                                    <p class="text-lg font-bold text-amber-600">₱{{ number_format($todayPrice->daily_price * $karat->multiplier, 0) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Mobile Footer Navigation -->
    <div class="safe-area-bottom lg:hidden border-t border-gray-200 bg-white/80 backdrop-blur-sm px-4 py-4 sticky bottom-0">
        <div class="flex items-center justify-between gap-2">
            <button class="flex-1 py-3 bg-gradient-to-r from-amber-600 to-yellow-500 text-white font-semibold rounded-lg transition active:opacity-80 text-center text-sm">
                🏠 Calculator
            </button>
            <a href="/receipts" class="flex-1 py-3 bg-purple-500 hover:bg-purple-600 text-white font-semibold rounded-lg transition active:opacity-80 text-center text-sm">
                📋 Receipts
            </a>
            <a href="/admin" class="flex-1 py-3 bg-gray-200 text-gray-800 font-semibold rounded-lg transition active:opacity-80 text-center text-sm">
                ⚙️ Admin
            </a>
        </div>
    </div>
</body>
</html>


