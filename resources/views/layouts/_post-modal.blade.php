@if (Auth::check())
    <div id="postModal" class="hidden fixed inset-0 bg-black/30 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg w-full max-w-xl p-4">
            <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data"
                class="flex flex-col gap-4">
                @csrf

                <!-- Avatar + Nội dung -->
                <div class="flex gap-3">
                    <!-- Avatar -->
                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}"
                        class="h-10 w-10 rounded-full object-cover border">

                    <!-- Nội dung -->
                    <div class="flex-1">
                        <!-- Tiêu đề -->
                        <input type="text" name="title" placeholder="Tiêu đề bài viết" required maxlength="255"
                            class="w-full text-base font-medium border-0 focus:ring-0 placeholder-gray-400" />

                        <!-- Nội dung -->
                        <textarea name="content" placeholder="Bạn đang nghĩ gì?" required
                            class="w-full text-base resize-none border-0 focus:ring-0 placeholder-gray-400 mt-2"></textarea>
                    </div>
                </div>

                <!-- Preview Media -->
                <div id="mediaPreview" class="mt-2 flex gap-2 overflow-x-auto"></div>

                <!-- Action row -->
                <div class="flex items-center justify-between border-t pt-2">
                    <!-- Media & hashtag -->
                    <div class="flex gap-3 text-gray-500">
                        <!-- Media upload -->
                        <label for="mediaInput"
                            class="flex items-center gap-1 cursor-pointer hover:text-black transition">
                            <i class="fa-sharp fa-thin fa-rectangle-plus text-lg"></i>
                            <span class="text-sm">Thêm ảnh/video</span>
                        </label>
                        <input type="file" name="media[]" id="mediaInput" multiple accept="image/*,video/*"
                            class="hidden" />

                        <!-- Hashtag -->
                        <input type="text" name="hashtag" placeholder="#hashtag"
                            class="text-sm border-0 focus:ring-0 placeholder-gray-400" />
                    </div>

                    <!-- Nút -->
                    <div class="flex gap-2">
                        <button type="button" id="closePostModal"
                            class="px-4 py-1.5 text-sm rounded-full border border-gray-300 text-gray-700 hover:bg-gray-100">
                            Hủy
                        </button>
                        <button type="submit"
                            class="px-4 py-1.5 bg-black text-white text-sm rounded-full hover:bg-gray-900 transition">
                            Đăng
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endif
