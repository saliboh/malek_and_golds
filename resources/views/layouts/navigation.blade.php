<!-- Global Navigation Bar -->
<div class="fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-gray-200 shadow-lg safe-area-bottom md:relative md:border-t-0 md:shadow-none md:border-r md:border-b-0 md:w-64 md:flex md:flex-col">
    <div class="flex md:flex-col justify-around md:justify-start md:space-y-2 px-2 md:px-0 py-2 md:py-4">
        <!-- Calculator Link -->
        <a href="/" class="flex flex-col md:flex-row items-center md:items-center gap-2 px-3 md:px-4 py-2 md:py-3 rounded-lg transition-all group
            {{ request()->is('/') ? 'bg-amber-100 text-amber-700 md:bg-amber-50 md:border-l-4 md:border-amber-600' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <span class="text-xl md:text-2xl">🧮</span>
            <span class="text-xs md:text-sm font-semibold hidden md:inline">Calculator</span>
        </a>

        <!-- Receipts Link -->
        <a href="/receipts" class="flex flex-col md:flex-row items-center md:items-center gap-2 px-3 md:px-4 py-2 md:py-3 rounded-lg transition-all group
            {{ request()->is('receipts*') ? 'bg-purple-100 text-purple-700 md:bg-purple-50 md:border-l-4 md:border-purple-600' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <span class="text-xl md:text-2xl">📋</span>
            <span class="text-xs md:text-sm font-semibold hidden md:inline">Receipts</span>
        </a>

        <!-- Admin Link -->
        <a href="/admin" class="flex flex-col md:flex-row items-center md:items-center gap-2 px-3 md:px-4 py-2 md:py-3 rounded-lg transition-all group
            {{ request()->is('admin*') ? 'bg-blue-100 text-blue-700 md:bg-blue-50 md:border-l-4 md:border-blue-600' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <span class="text-xl md:text-2xl">⚙️</span>
            <span class="text-xs md:text-sm font-semibold hidden md:inline">Admin</span>
        </a>

        <!-- Logout Link -->
        <form action="{{ route('logout') }}" method="POST" class="w-full md:w-auto">
            @csrf
            <button type="submit" class="w-full flex flex-col md:flex-row items-center md:items-center gap-2 px-3 md:px-4 py-2 md:py-3 rounded-lg transition-all text-gray-600 hover:bg-red-100 hover:text-red-700">
                <span class="text-xl md:text-2xl">🚪</span>
                <span class="text-xs md:text-sm font-semibold hidden md:inline">Logout</span>
            </button>
        </form>
    </div>
</div>

<!-- Mobile Spacer -->
<div class="h-20 md:h-0"></div>
