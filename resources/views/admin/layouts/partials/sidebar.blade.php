<aside class="fixed top-0 left-0 h-full bg-white border-r shadow-sm transition-all duration-300 z-40"
    :class="sidebarOpen ? 'w-64' : 'w-20'">

    {{-- Logo + toggle --}}
    <div class="h-16 flex items-center justify-between border-b px-4">
        <span class="text-xl font-bold truncate" x-show="sidebarOpen" x-transition>Admin</span>
        <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg hover:bg-gray-100">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>

    {{-- Menu --}}
    <nav class="flex-1 p-4 space-y-2">

        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100 
                   @if (request()->routeIs('admin.dashboard')) bg-gray-100 font-semibold @endif">
            <i class="fa-solid fa-chart-line mr-3"></i>
            <span x-show="sidebarOpen" x-transition>Dashboard</span>
        </a>

        {{-- Quản lý địa điểm --}}
        <div x-data="{ open: {{ request()->routeIs('admin.buildings.*') || request()->routeIs('admin.floors.*') || request()->routeIs('admin.rooms.*') ? 'true' : 'false' }} }" class="space-y-1">

            <button @click="open = !open"
                class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-gray-100
                       @if (request()->routeIs('admin.buildings.*') ||
                               request()->routeIs('admin.floors.*') ||
                               request()->routeIs('admin.rooms.*')) bg-gray-100 font-semibold @endif">
                <div class="flex items-center">
                    <i class="fa-solid fa-map-marker-alt mr-3"></i>
                    <span x-show="sidebarOpen" x-transition>Quản lý địa điểm</span>
                </div>
                <i class="fa-solid fa-chevron-down text-sm transition-transform" :class="open ? 'rotate-180' : ''"
                    x-show="sidebarOpen"></i>
            </button>

            <div x-show="open" x-transition class="pl-10 space-y-1">
                <a href="{{ route('admin.buildings.create') }}"
                    class="block px-3 py-2 rounded-lg hover:bg-gray-100 
                          @if (request()->routeIs('admin.buildings.*')) bg-gray-100 font-semibold @endif">
                    Tòa nhà
                </a>
                <a href="{{ route('admin.floors.create') }}"
                    class="block px-3 py-2 rounded-lg hover:bg-gray-100 
                          @if (request()->routeIs('admin.floors.*')) bg-gray-100 font-semibold @endif">
                    Tầng
                </a>
                <a href="{{ route('admin.rooms.create') }}"
                    class="block px-3 py-2 rounded-lg hover:bg-gray-100 
                          @if (request()->routeIs('admin.rooms.*')) bg-gray-100 font-semibold @endif">
                    Phòng học
                </a>
            </div>
        </div>

        {{-- Người dùng --}}
        <a href="{{ route('admin.users.index') }}"
            class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100 
                   @if (request()->routeIs('admin.users.*')) bg-gray-100 font-semibold @endif">
            <i class="fa-solid fa-users mr-3"></i>
            <span x-show="sidebarOpen" x-transition>Người dùng</span>
        </a>
        
        {{-- Bài viết --}}
        <a href="{{ route('admin.posts.index') }}"
            class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100 
                   @if (request()->routeIs('admin.posts.*')) bg-gray-100 font-semibold @endif">
            <i class="fa-solid fa-newspaper mr-3"></i>
            <span x-show="sidebarOpen" x-transition>Bài viết</span>
        </a>

        {{-- Bình luận --}}
        <a href="{{ route('admin.comments.index') }}"
            class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100 
                   @if (request()->routeIs('admin.comments.*')) bg-gray-100 font-semibold @endif">
            <i class="fa-solid fa-comments mr-3"></i>
            <span x-show="sidebarOpen" x-transition>Bình luận</span>
        </a>

        {{-- Báo cáo --}}
        <a href="{{ route('reports.index') }}"
            class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100 
                   @if (request()->routeIs('reports.*')) bg-gray-100 font-semibold @endif">
            <i class="fa-solid fa-flag mr-3 text-red-600"></i>
            <span x-show="sidebarOpen" x-transition class="flex-1">Báo cáo</span>

            {{-- Badge số báo cáo pending --}}
            @if (\App\Models\Report::pendingCount() > 0)
                <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full"
                    x-show="sidebarOpen" x-transition>
                    {{ \App\Models\Report::pendingCount() }}
                </span>
            @endif
        </a>

    </nav>

    {{-- Logout --}}
    <div class="border-t p-4">
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full flex items-center px-3 py-2 rounded-lg hover:bg-red-50 text-red-600">
                <i class="fa-solid fa-right-from-bracket mr-3"></i>
                <span x-show="sidebarOpen" x-transition>Đăng xuất</span>
            </button>
        </form>
    </div>
</aside>
