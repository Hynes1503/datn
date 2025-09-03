<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thread Interface with Navbar and Menu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans flex">
    <!-- Navbar (Thanh điều hướng bên trái) -->
    <nav class="w-64 bg-white h-screen fixed top-0 left-0 border-r border-gray-200 p-4">
        <div class="flex items-center mb-8">
            <h1 class="text-2xl font-bold text-blue-500">Threads</h1>
        </div>
        <ul class="space-y-4">
            <li>
               率先
                <a href="#" class="flex items-center space-x-2 text-gray-700 hover:text-blue-500 font-medium">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-2 2H3"></path></svg>
                    <span>Trang chủ</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center space-x-2 text-gray-700 hover:text-blue-500 font-medium">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 00-8 0v4H5a2 2 0 00-2 2v7a2 2 0 002 2h14a2 2 0 002-2v-7a2 2 0 00-2-2h-3V7z"></path></svg>
                    <span>Hồ sơ</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center space-x-2 text-gray-700 hover:text-blue-500 font-medium">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.5-1.5L12 7l-6.5 6.5L4 17h5m6 0v-5H9v5h6z"></path></svg>
                    <span>Thông báo</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center space-x-2 text-gray-700 hover:text-blue-500 font-medium">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 01-9-9m9 9H3m-9 0a9 9 0 019-9m0 18c-4.97 0-9-4.03-9-9m9 9H3"></path></svg>
                    <span>Tin nhắn</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center space-x-2 text-gray-700 hover:text-blue-500 font-medium">
                    <svg class="w-6 h-6" fill="none" stroke="

currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0L17 8m-6 8l4-4m0 0L7 8"></path></svg>
                    <span>Cài đặt</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Container chính -->
    <div class="max-w-2xl mx-auto bg-white min-h-screen ml-64">
        <!-- Form đăng bài -->
        <div class="p-4 border-b border-gray-200">
            <div class="flex space-x-3">
                <img src="https://via.placeholder.com/40" alt="User Avatar" class="w-10 h-10 rounded-full">
                <div class="flex-1">
                    <textarea class="w-full border rounded-lg p-3 text-gray-700 focus:outline-none" rows="3" placeholder="What's on your mind?"></textarea>
                    <div class="flex justify-end mt-2">
                        <button class="bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600">Post</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danh sách bài đăng -->
        <div class="divide-y divide-gray-200">
            <!-- Bài đăng 1 -->
            <div class="p-4 hover:bg-gray-50 relative group">
                <div class="flex space-x-3">
                    <img src="https://via.placeholder.com/40" alt="User Avatar" class="w-10 h-10 rounded-full">
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="font-semibold">John Doe</span>
                                <span class="text-gray-500 text-sm ml-2">@johndoe · 2h</span>
                            </div>
                            <!-- Nút menu ngữ cảnh -->
                            <button class="text-gray-500 hover:text-gray-700 focus:outline-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6h.01M12 12h.01M12 18h.01"></path></svg>
                            </button>
                        </div>
                        <p class="text-gray-800 mt-1">
                            This is an example thread post. It can contain text, links, or even images!
                        </p>
                        <div class="mt-3 flex space-x-6 text-gray-500">
                            <button class="flex items-center space-x-1 hover:text-blue-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                <span>12</span>
                            </button>
                            <button class="flex items-center space-x-1 hover:text-blue-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5v-4a2 2 0 012-2h10a2 2 0 012 2v4h-4M12 16h.01"></path></svg>
                                <span>8</span>
                            </button>
                            <button class="flex items-center space-x-1 hover:text-blue-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                <span>3</span>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Menu ngữ cảnh (ẩn, hiển thị khi nhấp vào nút "⋯") -->
                <div class="absolute right-4 top-10 hidden group-hover:block bg-white border border-gray-200 rounded-lg shadow-lg py-2 z-10">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Chỉnh sửa</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Xóa bài đăng</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Báo cáo</a>
                </div>
            </div>

            <!-- Bài đăng 2 -->
            <div class="p-4 hover:bg-gray-50 relative group">
                <div class="flex space-x-3">
                    <img src="https://via.placeholder.com/40" alt="User Avatar" class="w-10 h-10 rounded-full">
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="font-semibold">Jane Smith</span>
                                <span class="text-gray-500 text-sm ml-2">@janesmith · 5h</span>
                            </div>
                            <!-- Nút menu ngữ cảnh -->
                            <button class="text-gray-500 hover:text-gray-700 focus:outline-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6h.01M12 12h.01M12 18h.01"></path></svg>
                            </button>
                        </div>
                        <p class="text-gray-800 mt-1">
                            Loving the new features on this platform! What's your favorite? 😄
                        </p>
                        <div class="mt-3 flex space-x-6 text-gray-500">
                            <button class="flex items-center space-x-1 hover:text-blue-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                <span>25</span>
                            </button>
                            <button class="flex items-center space-x-1 hover:text-blue-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5v-4a2 2 0 012-2h10a2 2 0 012 2v4h-4M12 16h.01"></path></svg>
                                <span>15</span>
                            </button>
                            <button class="flex items-center space-x-1 hover:text-blue-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                <span>7</span>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Menu ngữ cảnh (ẩn, hiển thị khi nhấp vào nút "⋯") -->
                <div class="absolute right-4 top-10 hidden group-hover:block bg-white border border-gray-200 rounded-lg shadow-lg py-2 z-10">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Chỉnh sửa</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Xóa bài đăng</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Báo cáo</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>