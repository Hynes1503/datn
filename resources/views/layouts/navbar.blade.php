<nav class="fixed top-0 left-0 h-full w-20 bg-white border-r border-gray-200 flex flex-col items-center py-6 z-50">
    <!-- Logo -->
    <a href="{{ route('home') }}" class="mb-8">
        <img src="{{ asset('images/threads-logo.svg') }}" alt="Threads" class="h-8 w-8">
    </a>

    <!-- Navigation -->
    <div class="flex flex-col items-center gap-8 flex-1">
        <!-- Home -->
        <a href="{{ route('home') }}"
            class="text-gray-900 hover:scale-110 transition-transform {{ request()->routeIs('home') ? 'font-bold' : '' }}"
            title="Trang chủ">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7m-9 5v6h4v-6m-7-9h14" />
            </svg>
        </a>

        <!-- Search -->
        <a href="#" class="text-gray-900 hover:scale-110 transition-transform" title="Tìm kiếm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </a>

        <!-- Create Post -->
        <a href="#" class="text-gray-900 hover:scale-110 transition-transform open-post-modal" title="Đăng bài">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
        </a>

        <!-- Activity -->
        <a href="#" class="text-gray-900 hover:scale-110 transition-transform" title="Hoạt động">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5
                         4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5
                         4.5 0 00-6.364 0z" />
            </svg>
        </a>

        <!-- Profile -->
        {{-- <a href="{{ route('users.show', Auth::user()->mention) }}"
           class="text-gray-900 hover:scale-110 transition-transform"
           title="Hồ sơ">
            <img src="{{ Auth::user()->avatar ? asset('storage/'.Auth::user()->avatar) : asset('images/default-avatar.png') }}"
                 class="h-8 w-8 rounded-full object-cover border" alt="Avatar">
        </a> --}}
    </div>

    <!-- Logout -->
    {{-- @if (Auth::check())
        <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="mt-6">
            @csrf
            <button type="submit" class="text-gray-900 hover:scale-110 transition-transform" title="Đăng xuất">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 
                             4v1a3 3 0 01-3 3H6a3 3 
                             0 01-3-3V7a3 3 0 013-3h3a3 
                             3 0 013 3v1"/>
                </svg>
            </button>
        </form>
    @endif --}}

    <!-- Profile & Logout Dropdown -->
    @if (Auth::check())
        <div class="relative">
            <!-- Avatar button -->
            <button id="avatarMenuBtn" type="button" class="flex items-center focus:outline-none">
                <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : asset('images/default-avatar.png') }}"
                    class="h-8 w-8 rounded-full object-cover border" alt="Avatar">
            </button>

            <!-- Dropdown menu -->
            <div id="avatarMenu"
                class="absolute bottom-0 left-10 mb-10 hidden bg-white border border-gray-200 rounded-xl shadow-lg w-40 z-50">
                <a href="{{ route('users.show', Auth::user()->mention) }}"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-t-xl">
                    Hồ sơ
                </a>
                <form id="logoutForm" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-b-xl">
                        Đăng xuất
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal -->
    @if (Auth::check())
        <div id="postModal" class="hidden">
            <div class="bg-white rounded-2xl shadow-md w-full max-w-lg p-5">
                <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data"
                    class="flex flex-col">
                    @csrf

                    <!-- Avatar + Nội dung -->
                    <div class="flex gap-3">
                        <!-- Avatar -->
                        <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : asset('images/default-avatar.png') }}"
                            alt="{{ Auth::user()->name }}" class="h-10 w-10 rounded-full object-cover border">

                        <!-- Input khu vực -->
                        <div class="flex-1 space-y-2">
                            <!-- Tiêu đề -->
                            <input type="text" name="title" placeholder="Tiêu đề bài viết" required maxlength="255"
                                class="w-full text-base border-b border-gray-200 focus:border-gray-400 focus:outline-none px-1 py-1" />

                            <!-- Nội dung -->
                            <textarea name="content" placeholder="Bạn đang nghĩ gì?" required
                                class="w-full text-base text-gray-800 placeholder-gray-400 resize-none focus:outline-none"></textarea>
                        </div>
                    </div>

                    <!-- Thêm media + hashtag -->
                    <div class="mt-3 space-y-2">
                        <!-- Media upload (ảnh + video) -->
                        <input type="file" name="media[]" multiple accept="image/*,video/*"
                            class="w-full text-sm text-gray-600" />

                        <!-- Hashtag -->
                        <input type="text" name="hashtag" placeholder="Hashtags (cách nhau bằng dấu phẩy)"
                            maxlength="255"
                            class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 focus:border-gray-400 focus:outline-none" />
                    </div>

                    <!-- Footer actions -->
                    <div class="flex justify-end mt-4 gap-2">
                        <button type="button" id="closePostModal"
                            class="px-4 py-1.5 text-sm rounded-full border border-gray-300 text-gray-700 hover:bg-gray-100">
                            Hủy
                        </button>
                        <button type="submit"
                            class="px-4 py-1.5 bg-black text-white text-sm rounded-full hover:bg-gray-900 transition">
                            Đăng
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
    <style>
        /* Styling for modal */
        #postModal {
            z-index: 1000;
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #postModal.hidden {
            display: none;
        }

        .modal-form textarea {
            width: 100%;
            min-height: 100px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            resize: vertical;
            font-size: 14px;
            color: #1f2937;
        }

        .modal-form textarea:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .modal-form input[type="text"] {
            width: 100%;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 14px;
            color: #1f2937;
            margin-top: 8px;
        }

        .modal-form input[type="text"]:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .modal-form input[type="file"] {
            width: 100%;
            margin-top: 8px;
            font-size: 14px;
            color: #1f2937;
        }

        .modal-form button {
            margin-top: 12px;
            padding: 8px 16px;
            background-color: #2563eb;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: medium;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .modal-form button:hover {
            background-color: #1d4ed8;
        }

        .modal-form button:disabled {
            background-color: #9ca3af;
            cursor: not-allowed;
        }

        .modal-form .cancel-button {
            background-color: #e5e7eb;
            color: #1f2937;
        }

        .modal-form .cancel-button:hover {
            background-color: #d1d5db;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const avatarBtn = document.getElementById('avatarMenuBtn');
            const avatarMenu = document.getElementById('avatarMenu');

            // Toggle menu khi bấm vào avatar
            avatarBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                avatarMenu.classList.toggle('hidden');
            });

            // Bấm ra ngoài thì ẩn menu
            document.addEventListener('click', (e) => {
                if (!avatarMenu.classList.contains('hidden') && !avatarMenu.contains(e.target) && e
                    .target !== avatarBtn) {
                    avatarMenu.classList.add('hidden');
                }
            });
        });
        // JavaScript for modal
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('postModal');
            const openModalButtons = document.querySelectorAll('.open-post-modal, #openPostModal');
            const closeModalBtn = document.getElementById('closePostModal');

            openModalButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    console.log('Open modal triggered from:', e.target); // Debug
                    modal.classList.remove('hidden');
                });
            });

            closeModalBtn.addEventListener('click', () => {
                console.log('Close modal clicked'); // Debug
                modal.classList.add('hidden');
            });

            // Close modal when clicking outside
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    console.log('Clicked outside modal'); // Debug
                    modal.classList.add('hidden');
                }
            });
        });
    </script>
</nav>
