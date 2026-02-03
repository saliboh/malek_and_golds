<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Malek & Golds - Admin Panel</title>
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
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type="number"] {
            -moz-appearance: textfield;
        }
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1), 0 0 0 1px rgba(59, 130, 246, 0.5);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-indigo-50 safe-area-top safe-area-bottom">
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Global Navigation -->
        @include('layouts.navigation')

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white sticky top-0 z-10 shadow-lg">
            <div class="px-4 py-6">
                <div class="flex items-center justify-between mb-3">
                    <h1 class="text-2xl lg:text-3xl font-bold tracking-tight">💎 Admin Panel</h1>
                    <span class="text-sm text-blue-100">Manage Gold Prices</span>
                </div>
                <p class="text-blue-100 text-sm">Manage Daily Gold Prices</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 px-4 py-6">
            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-md">
                    <p class="text-green-800 font-semibold flex items-center gap-2">
                        <span class="text-xl">✅</span> Success
                    </p>
                    <p class="text-green-700 text-sm mt-1">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-md">
                    <p class="text-red-800 font-semibold flex items-center gap-2">
                        <span class="text-xl">⚠️</span> Error
                    </p>
                    @foreach ($errors->all() as $error)
                        <p class="text-red-700 text-sm mt-1">• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Market Price Display (From Web) -->
            @if (session('marketPrice'))
                <div class="mb-6 bg-gradient-to-br from-orange-50 to-amber-50 border-2 border-orange-400 rounded-2xl p-6 shadow-lg">
                    <p class="text-orange-800 text-sm font-semibold mb-2 flex items-center gap-2">
                        <span class="text-lg">📈</span> MARKET RATE (Live from goldpricez.com)
                    </p>
                    <div class="flex items-baseline gap-2 mb-3">
                        <span class="text-4xl font-bold text-orange-600">₱{{ number_format(session('marketPrice'), 2) }}</span>
                        <span class="text-sm text-orange-600 font-semibold">/gram</span>
                    </div>
                    <p class="text-xs text-orange-600">🔗 Reference only - does not affect your daily price below</p>
                </div>
            @endif

            <!-- Current Price Display -->
            @if ($todayPrice)
                <div class="mb-6 bg-gradient-to-br from-blue-100 to-indigo-100 border-2 border-blue-400 rounded-2xl p-6 shadow-lg">
                    <p class="text-blue-800 text-sm font-semibold mb-2">📊 TODAY'S RATE</p>
                    <div class="flex items-baseline gap-2 mb-3">
                        <span class="text-4xl font-bold text-blue-600">₱{{ number_format($todayPrice->daily_price, 0) }}</span>
                        <span class="text-sm text-blue-600 font-semibold">/gram</span>
                    </div>
                    <p class="text-sm text-blue-700">Last updated: <span class="font-semibold">{{ $todayPrice->date->format('M d, Y') }}</span></p>
                    <p class="text-xs text-blue-600 mt-1">Change the value below to update today's rate</p>
                </div>
            @else
                <div class="mb-6 bg-yellow-50 border-2 border-yellow-400 rounded-2xl p-6 shadow-md">
                    <p class="text-yellow-800 font-semibold flex items-center gap-2 mb-2">
                        <span class="text-xl">⚡</span> No Price Set Yet
                    </p>
                    <p class="text-yellow-700 text-sm">Enter today's gold price below to get started</p>
                </div>
            @endif

            <!-- Fetch Market Gold Price from Web -->
            <div class="mb-6 bg-gradient-to-br from-purple-50 to-pink-50 border-2 border-purple-400 rounded-2xl p-6 shadow-lg">
                <p class="text-purple-800 text-sm font-semibold mb-3 flex items-center gap-2">
                    <span class="text-lg">🌐</span> Check Market Price
                </p>
                <p class="text-purple-700 text-sm mb-4">View the current market gold price from goldpricez.com for reference. This does NOT change your daily price below.</p>
                <form action="{{ route('fetch-gold-price') }}" method="POST" class="inline">
                    @csrf
                    <button
                        type="submit"
                        class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-bold py-3 px-4 rounded-xl transition transform active:scale-95 shadow-lg"
                    >
                        📊 Check Market Rate
                    </button>
                </form>
                <p class="text-xs text-purple-600 mt-2">Source: <a href="https://goldpricez.com/ph/gram" target="_blank" class="underline hover:text-purple-800">goldpricez.com/ph/gram</a></p>
            </div>

            <!-- Input Form -->
            <form action="/admin/gold-prices" method="POST" class="space-y-4">
                @csrf

                <!-- Price Input -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-800 ml-1">
                        <span class="text-lg">💰</span> Today's Gold Price (per gram)
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-4 text-2xl text-blue-600">₱</span>
                        <input
                            type="number"
                            name="daily_price"
                            step="0.01"
                            placeholder="Enter daily gold price"
                            value="{{ $todayPrice?->daily_price ?? '' }}"
                            class="input-focus w-full pl-12 pr-4 py-4 text-lg bg-white border-2 border-gray-200 rounded-xl focus:border-blue-500 transition placeholder-gray-400"
                            required
                        >
                    </div>
                    <p class="text-xs text-gray-500 ml-1">e.g., 8500, 9200, 10500</p>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-4">
                    <p class="text-xs text-blue-800">
                        <span class="font-semibold">💡 Tip:</span> This price is the market rate that customers will get calculated based on their gold weight and karat purity.
                    </p>
                </div>

                <!-- Update Button -->
                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-4 px-4 rounded-xl transition transform active:scale-95 shadow-lg text-lg mt-6"
                >
                    ✨ Update Daily Price
                </button>
            </form>

            <!-- Additional Info -->
            <div class="mt-8 bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <span class="text-lg">ℹ️</span> How It Works
                </h3>
                <div class="space-y-2 text-sm text-gray-700">
                    <p>• Set the daily market gold price above</p>
                    <p>• Customers use the calculator to get prices for their gold</p>
                    <p>• The system automatically calculates based on karat purity</p>
                    <p>• Our buying offers show profit margins for your business</p>
                </div>
            </div>

            <!-- Statistics -->
            @if ($todayPrice)
                <div class="mt-6 bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-xl p-5 shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                        <span class="text-lg">📈</span> Quick Reference
                    </h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-700">24K (Purest)</span>
                            <span class="font-bold text-green-600">₱{{ number_format($todayPrice->daily_price * 0.999, 0) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">18K (Common)</span>
                            <span class="font-bold text-green-600">₱{{ number_format($todayPrice->daily_price * 0.750, 0) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">14K (Standard)</span>
                            <span class="font-bold text-green-600">₱{{ number_format($todayPrice->daily_price * 0.585, 0) }}</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Footer Navigation -->
        <div class="safe-area-bottom border-t border-gray-200 bg-white/80 backdrop-blur-sm px-4 py-4">
            <div class="flex items-center justify-between gap-2">
                <a href="/" class="flex-1 py-3 bg-gray-200 text-gray-800 font-semibold rounded-lg transition active:opacity-80 text-center">
                    🏠 Calculator
                </a>
                <button class="flex-1 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-lg transition active:opacity-80">
                    ⚙️ Admin
                </button>
            </div>
        </div>
    </div>
</body>
</html>
