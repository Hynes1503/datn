@if (Auth::check())
    <div class="relative">
        <!-- Avatar button -->
        <button id="avatarMenuBtn" type="button"
            class="flex items-center hover:scale-110 hover:bg-gray-100 p-2 rounded-full transition-all focus:outline-none">
            <img src="{{ Auth::user()->avatar_url }}" class="h-8 w-8 rounded-full object-cover border"
                alt="Avatar">
        </button>

        <!-- Dropdown menu -->
        <div id="avatarMenu"
            class="absolute left-10 bottom-0 mb-2 hidden bg-white border border-gray-200 rounded-xl shadow-lg w-80 z-50">
            
            <!-- Hồ sơ -->
            <a href="{{ route('users.show', Auth::user()->mention) }}"
                class="flex justify-between items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 border-b border-gray-100 transition-colors">
                <span>Hồ sơ</span>
                <i class="fa-solid fa-user text-gray-500"></i>
            </a>

            <!-- Bài viết đã thích -->
            <a href="{{ route('posts.liked') }}"
                class="flex justify-between items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 border-b border-gray-100 transition-colors">
                <span>Bài viết đã thích</span>
                <i class="fa-solid fa-heart text-gray-500"></i>
            </a>

            <!-- Đăng xuất -->
            <form id="logoutForm" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="flex justify-between items-center w-full px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 rounded-b-xl transition-colors">
                    <span>Đăng xuất</span>
                    <i class="fa-solid fa-right-from-bracket text-gray-500"></i>
                </button>
            </form>
        </div>
    </div>
@endif
