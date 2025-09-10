<!-- resources/views/components/navbar/_nav-items.blade.php -->
<div class="flex flex-col items-center justify-center gap-8 flex-1 h-full">
    <!-- Home -->
    <a href="{{ route('home') }}"
        class="text-gray-900 hover:scale-110 hover:bg-gray-100 p-2 rounded-full transition-all {{ request()->routeIs('home') ? 'font-bold' : '' }}"
        title="Trang chủ">
        <i class="fa-solid fa-house"></i>
    </a>

    <!-- Search -->
    @include('layouts._search')

    <!-- Create Post -->
    <a href="#"
        class="text-gray-900 hover:scale-110 hover:bg-gray-100 p-2 rounded-full transition-all open-post-modal"
        title="Đăng bài">
        <i class="fa-solid fa-plus"></i>
    </a>

    @include('layouts._notifications-dropdown')
    @include('layouts._profile-dropdown')
</div>
