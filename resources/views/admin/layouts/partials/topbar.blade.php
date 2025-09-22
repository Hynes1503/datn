<header class="h-16 bg-white border-b shadow-sm flex items-center justify-between px-4 sticky top-0 z-30">
    <div class="flex items-center space-x-2">
        {{-- Toggle cho mobile --}}
        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 rounded-lg hover:bg-gray-100">
            <i class="fa-solid fa-bars"></i>
        </button>
        <h1 class="text-lg font-semibold">@yield('page-title', 'Tổng quan hệ thống')</h1>
    </div>
    <div class="flex items-center space-x-4">
        <div class="relative group">
            <button class="flex items-center space-x-2 focus:outline-none">
                <img src="https://ui-avatars.com/api/?name=Admin" alt="avatar" class="w-8 h-8 rounded-full">
                <span class="hidden md:inline text-sm font-medium">Admin</span>
            </button>
            <div class="absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg hidden group-hover:block">
                <a href="#" class="block px-4 py-2 hover:bg-gray-100">Hồ sơ</a>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">Đăng xuất</button>
                </form>
            </div>
        </div>
    </div>
</header>
