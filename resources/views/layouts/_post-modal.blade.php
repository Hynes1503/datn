@if (Auth::check())
    <div id="postModal" class="hidden">
        <div class="bg-white rounded-2xl shadow-md w-full max-w-lg p-5">
            <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data"
                class="flex flex-col modal-form">
                @csrf

                <!-- Avatar + Nội dung -->
                <div class="flex gap-3">
                    <!-- Avatar -->
                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}"
                        class="h-10 w-10 rounded-full object-cover border">

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

                <!-- Preview Media -->
                <div id="mediaPreview" class="mt-3 flex flex-row gap-2 overflow-x-auto white-space-nowrap"></div>

                <!-- Thêm media + hashtag -->
                <div class="mt-3 space-y-2">
                    <!-- Media upload (ảnh + video) -->
                    <input type="file" name="media[]" multiple accept="image/*,video/*"
                        class="w-full text-sm text-gray-600" id="mediaInput" />

                    <!-- Hashtag -->
                    <input type="text" name="hashtag" placeholder="Hashtags (cách nhau bằng dấu phẩy)"
                        maxlength="255"
                        class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 focus:border-gray-400 focus:outline-none" />
                </div>

                <!-- Footer actions -->
                <div class="flex justify-end mt-4 gap-2">
                    <button type="button" id="closePostModal"
                        class="px-4 py-1.5 text-sm rounded-full border border-gray-300 text-gray-700 hover:bg-gray-100 cancel-button">
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