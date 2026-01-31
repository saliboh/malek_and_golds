<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Login - Malek & Golds</title>
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
        input[type="email"]::-webkit-outer-spin-button,
        input[type="password"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1), 0 0 0 1px rgba(59, 130, 246, 0.5);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-amber-50 via-white to-yellow-50 safe-area-top safe-area-bottom">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-md">
            <!-- Logo and Title -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-amber-600 mb-2">💎 Malek & Golds</h1>
                <p class="text-gray-600">Gold Price Calculator</p>
            </div>

            <!-- Login Card -->
            <div class="bg-white rounded-2xl shadow-xl p-8 space-y-6">
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Welcome Back</h2>
                    <p class="text-gray-600 text-sm mt-1">Sign in to your account</p>
                </div>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                        <p class="text-red-800 font-semibold text-sm">⚠️ Login Failed</p>
                        @foreach ($errors->all() as $error)
                            <p class="text-red-700 text-sm mt-1">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Email Input -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-800">
                            📧 Email Address
                        </label>
                        <input
                            type="email"
                            name="email"
                            placeholder="Enter your email"
                            value="{{ old('email') }}"
                            class="input-focus w-full px-4 py-3 text-base bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition placeholder-gray-400"
                            required
                        >
                    </div>

                    <!-- Password Input -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-800">
                            🔐 Password
                        </label>
                        <input
                            type="password"
                            name="password"
                            placeholder="Enter your password"
                            class="input-focus w-full px-4 py-3 text-base bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-blue-500 transition placeholder-gray-400"
                            required
                        >
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            name="remember"
                            id="remember"
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        >
                        <label for="remember" class="ml-2 text-sm text-gray-600">
                            Remember me
                        </label>
                    </div>

                    <!-- Login Button -->
                    <button
                        type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-3 px-4 rounded-xl transition transform active:scale-95 shadow-lg text-lg mt-6"
                    >
                        ✨ Sign In
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div class="text-center mt-8 text-gray-600 text-sm">
                <p>© 2026 Malek & Golds. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
