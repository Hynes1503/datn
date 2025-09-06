<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .post-images {
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding: 0.25rem 0.5rem;
            position: relative;
        }

        .post-images::-webkit-scrollbar {
            height: 8px;
        }

        .post-images::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.15);
            border-radius: 9999px;
        }

        .post-image {
            height: 250px;
            width: auto;
            max-width: 100%;
            border-radius: 0.5rem;
            object-fit: cover;
            display: block;
            flex: 0 0 auto;
        }

        .image-box {
            height: 250px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #f8fafc;
            border-radius: 0.5rem;
            flex: 0 0 auto;
        }
        .post-images.scrolled-to-end::after {
            display: none;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .dropdown-container {
            position: relative;
            display: inline-block;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            z-index: 20;
            min-width: 150px;
        }

        .dropdown-menu.active {
            display: block;
        }

        .dropdown-menu a {
            display: block;
            padding: 0.5rem 1rem;
            color: #4b5563;
            text-decoration: none;
        }

        .dropdown-menu a:hover {
            background: #f3f4f6;
            color: #111827;
        }
    </style>
</head>

<body class="bg-gray-200 text-gray-900 min-h-screen flex">
    <!-- Navbar -->
    @include('layouts.navbar')

    <!-- Content Area -->
    <div class="flex-1 flex justify-center p-4">
        <div class="w-full max-w-3xl">
            <!-- Dropdown Trigger Button -->
            <div class="flex justify-center mb-4 dropdown-container">
                <button id="dropdownToggle"
                    class="text-gray-600 hover:text-gray-900 bg-white px-4 py-2 rounded-md shadow-sm flex items-center gap-2">
                    <i id="dropdownIcon" class="fa-solid fa-chevron-right"></i>
                    <span id="dropdownLabel">@yield('menuTitle', 'Danh mục')</span>
                </button>
                <!-- Dropdown Menu -->
                <div id="dropdownMenu" class="dropdown-menu">
                    <a href="{{ route('users.show', Auth::user()->mention) }}">Trang cá nhân</a>
                    <a href="{{ route('home') }}">Sự kiện mới</a>
                    <a href="#">Tìm đồ/Mất đồ</a>
                </div>
            </div>
            @yield('content')
        </div>
    </div>

    <script>
        // --- Handle dropdown ---
        const dropdownToggle = document.getElementById('dropdownToggle');
        const dropdownMenu = document.getElementById('dropdownMenu');
        const dropdownLabel = document.getElementById('dropdownLabel');
        const dropdownIcon = document.getElementById('dropdownIcon');

        // Toggle mở/đóng dropdown + đổi icon
        dropdownToggle.addEventListener('click', () => {
            dropdownMenu.classList.toggle('active');
            dropdownIcon.classList.toggle('fa-chevron-right');
            dropdownIcon.classList.toggle('fa-chevron-down');
        });

        // Khi chọn option trong menu
        dropdownMenu.querySelectorAll('a').forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                dropdownLabel.textContent = item.textContent;
                dropdownMenu.classList.remove('active');

                // Reset icon về chevron-right
                dropdownIcon.classList.remove('fa-chevron-down');
                dropdownIcon.classList.add('fa-chevron-right');

                // Điều hướng
                window.location.href = item.getAttribute('href');
            });
        });

        // Đóng dropdown khi click ra ngoài
        document.addEventListener('click', (event) => {
            if (!dropdownToggle.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.classList.remove('active');
                dropdownIcon.classList.remove('fa-chevron-down');
                dropdownIcon.classList.add('fa-chevron-right');
            }
        });

        // --- Handle logout confirm ---
        const logoutForm = document.getElementById('logoutForm');
        if (logoutForm) {
            logoutForm.addEventListener('submit', (e) => {
                const confirmed = confirm("Bạn có chắc chắn muốn đăng xuất không?");
                if (!confirmed) {
                    e.preventDefault(); // Hủy submit nếu chọn Cancel
                }
            });
        }
    </script>
</body>

</html>