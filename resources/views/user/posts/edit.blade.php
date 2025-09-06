@extends('layouts.app')

@section('title', 'Sửa bài viết')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-2xl font-bold mb-6">Sửa bài viết</h2>

            <form action="{{ route('posts.update', ['user' => $user->mention, 'post' => $post->slug]) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Title --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tiêu đề</label>
                    <input type="text" name="title"
                        class="w-full p-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-600"
                        value="{{ old('title', $post->title) }}" required>
                    @error('title')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Content --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nội dung</label>
                    <textarea name="content"
                        class="w-full p-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-600"
                        rows="5" required>{{ old('content', $post->content) }}</textarea>
                    @error('content')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Hashtags --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Hashtag</label>
                    <input type="text" name="hashtag"
                        class="w-full p-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-600"
                        value="{{ old('hashtag', $post->hashtag) }}" placeholder="Nhập hashtag, cách nhau bằng dấu phẩy">
                    @error('hashtag')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Current Media + Add New Media --}}
                @if ($post->media && $post->media->count())
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Media</label>
                        <div class="flex gap-3 flex-wrap items-start">
                            @foreach ($post->media as $m)
                                <div class="relative group w-40">
                                    {{-- Media --}}
                                    @if ($m->media_type === 'image')
                                        <img src="{{ asset('storage/' . $m->media_path) }}" alt="Ảnh bài viết"
                                            class="post-image w-full h-32 object-cover rounded-md border" loading="lazy">
                                    @elseif ($m->media_type === 'video')
                                        <video controls class="w-full h-32 object-cover rounded-md border">
                                            <source src="{{ asset('storage/' . $m->media_path) }}" type="video/mp4">
                                            Trình duyệt không hỗ trợ video.
                                        </video>
                                    @endif

                                    {{-- Delete overlay button --}}
                                    <label
                                        class="absolute top-1 right-1 bg-red-600 text-white text-xs px-2 py-1 rounded-md 
                        opacity-0 group-hover:opacity-100 cursor-pointer transition">
                                        <input type="checkbox" name="delete_media[]" value="{{ $m->id }}"
                                            class="hidden">
                                        Xóa
                                    </label>
                                </div>
                            @endforeach

                            {{-- Add new media button --}}
                            <div
                                class="w-40 h-32 flex items-center justify-center border border-dashed rounded-md cursor-pointer hover:bg-gray-100">
                                <label
                                    class="flex flex-col items-center justify-center cursor-pointer w-full h-full text-gray-500">
                                    <i class="fa-solid fa-plus text-xl mb-1"></i>
                                    <span class="text-xs">Thêm media</span>
                                    <input type="file" name="media[]" class="hidden" multiple accept="image/*,video/*">
                                </label>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Đánh dấu media muốn xóa hoặc thêm mới.</p>
                    </div>
                @else
                    {{-- Nếu chưa có media nào, vẫn hiện nút thêm --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Media</label>
                        <div
                            class="w-40 h-32 flex items-center justify-center border border-dashed rounded-md cursor-pointer hover:bg-gray-100">
                            <label
                                class="flex flex-col items-center justify-center cursor-pointer w-full h-full text-gray-500">
                                <i class="fa-solid fa-plus text-xl mb-1"></i>
                                <span class="text-xs">Thêm media</span>
                                <input type="file" name="media[]" class="hidden" multiple accept="image/*,video/*">
                            </label>
                        </div>
                    </div>
                @endif

                {{-- Submit Button --}}
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                    Cập nhật
                </button>
            </form>
        </div>

        {{-- Comment Section --}}
        <div class="bg-white rounded-2xl shadow-sm p-6 mt-6">
            <h2 class="text-2xl font-bold mb-6">Bình luận</h2>

            {{-- Comment Form --}}
            @auth
                <form action="{{ route('comments.store', ['user' => $user->mention, 'post' => $post->slug]) }}"
                    method="POST" class="mb-6">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Thêm bình luận</label>
                        <textarea name="content"
                            class="w-full p-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-600"
                            rows="3" required placeholder="Viết bình luận của bạn..."></textarea>
                        @error('content')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                        Gửi bình luận
                    </button>
                </form>
            @else
                <p class="text-sm text-gray-600 mb-4">Vui lòng <a href="{{ route('login') }}"
                        class="text-blue-600 hover:underline">đăng nhập</a> để bình luận.</p>
            @endauth

            {{-- Comments Display --}}
            <div id="comments-list">
                @foreach ($post->comments as $comment)
                    <div class="comment mb-4 border-l-4 border-blue-600 pl-4">
                        <div class="flex items-center mb-2">
                            <img src="{{ $comment->user->avatar_url }}"
                                alt="{{ $comment->user->name }}"
                                class="w-8 h-8 rounded-full mr-2">
                            <div>
                                <a href="{{ route('users.show', $comment->user->mention) }}"
                                    class="text-sm font-semibold text-gray-700 hover:underline">{{ $comment->user->name }}</a>
                                <p class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-700">{{ $comment->content }}</p>

                        {{-- Reply Form --}}
                        @auth
                            <form action="{{ route('comments.store', ['user' => $user->mention, 'post' => $post->slug]) }}"
                                method="POST" class="mt-2">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                <div class="mb-2">
                                    <textarea name="content"
                                        class="w-full p-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-600"
                                        rows="2" placeholder="Trả lời bình luận..."></textarea>
                                    @error('content')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit"
                                    class="px-3 py-1 bg-blue-600 text-white rounded-md text-xs hover:bg-blue-700">
                                    Trả lời
                                </button>
                            </form>
                        @endauth

                        {{-- Delete Comment (for comment owner) --}}
                        @auth
                            @if (auth()->user()->id === $comment->user_id)
                                <form action="{{ route('comments.destroy', ['user' => $user->mention, 'post' => $post->slug, 'comment' => $comment->id]) }}"
                                    method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-xs text-red-600 hover:underline mt-2">Xóa</button>
                                </form>
                            @endif
                        @endauth

                        {{-- Replies --}}
                        @if ($comment->replies->count())
                            <div class="ml-6 mt-4">
                                @foreach ($comment->replies as $reply)
                                    <div class="comment mb-4 border-l-2 border-gray-300 pl-4">
                                        <div class="flex items-center mb-2">
                                            <img src="{{ $reply->user->avatar_url }}"
                                                alt="{{ $reply->user->name }}"
                                                class="w-6 h-6 rounded-full mr-2">
                                            <div>
                                                <a href="{{ route('users.show', $reply->user->mention) }}"
                                                    class="text-sm font-semibold text-gray-700 hover:underline">{{ $reply->user->name }}</a>
                                                <p class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-700">{{ $reply->content }}</p>

                                        {{-- Delete Reply (for reply owner) --}}
                                        @auth
                                            @if (auth()->user()->id === $reply->user_id)
                                                <form action="{{ route('comments.destroy', ['user' => $user->mention, 'post' => $post->slug, 'comment' => $reply->id]) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-xs text-red-600 hover:underline mt-2">Xóa</button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Lấy tất cả các checkbox delete media
            const deleteLabels = document.querySelectorAll('label input[name="delete_media[]"]');

            deleteLabels.forEach(input => {
                const label = input.closest('label');

                label.addEventListener('click', function(e) {
                    e.preventDefault(); // ngăn checkbox mặc định
                    const mediaWrapper = label.closest('.relative');

                    // Bật/tắt checkbox
                    input.checked = !input.checked;

                    // Nếu checked, ẩn ảnh/video ngay lập tức
                    if (input.checked) {
                        mediaWrapper.style.display = 'none';
                    } else {
                        mediaWrapper.style.display = 'block';
                    }
                });
            });
        });
    </script>
@endsection