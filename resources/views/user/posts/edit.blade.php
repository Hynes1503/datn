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

                {{-- Address --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Địa điểm</label>
                    <select name="room_id"
                        class="w-full p-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
                        <option value="">-- Chọn Phòng --</option>
                        @foreach ($buildings as $building)
                            <optgroup label="{{ $building->name }}">
                                @foreach ($building->floors as $floor)
                                    @foreach ($floor->rooms as $room)
                                        <option value="{{ $room->id }}"
                                            {{ old('room_id', $post->room_id) == $room->id ? 'selected' : '' }}>
                                            {{ $floor->name ?? 'Tầng ' . $floor->floor_number }} - {{ $room->name ?? $room->room_number }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('room_id')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Current Media + Add New Media --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Media</label>
                    <div class="flex gap-3 flex-wrap items-start" id="media-container">
                        @if ($post->media && $post->media->count())
                            @foreach ($post->media as $m)
                                <div class="relative group w-40" data-media-id="{{ $m->id }}">
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
                                        <i class="fa-solid fa-trash"></i>
                                    </label>
                                </div>
                            @endforeach
                        @endif

                        {{-- Add new media button --}}
                        <div
                            class="w-40 h-32 flex items-center justify-center border border-dashed rounded-md cursor-pointer hover:bg-gray-100">
                            <label
                                class="flex flex-col items-center justify-center cursor-pointer w-full h-full text-gray-500">
                                <i class="fa-solid fa-plus text-xl mb-1"></i>
                                <span class="text-xs">Thêm media</span>
                                <input type="file" name="media[]" id="media-input" class="hidden" multiple
                                    accept="image/*,video/*">
                            </label>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Đánh dấu media muốn xóa hoặc thêm mới.</p>
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                    Cập nhật
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle delete media functionality
            const deleteLabels = document.querySelectorAll('label input[name="delete_media[]"]');
            deleteLabels.forEach(input => {
                const label = input.closest('label');
                label.addEventListener('click', function(e) {
                    e.preventDefault(); // Prevent default checkbox behavior
                    const mediaWrapper = label.closest('.relative');
                    input.checked = !input.checked;
                    mediaWrapper.style.display = input.checked ? 'none' : 'block';
                });
            });

            // Handle media preview for new files
            const mediaInput = document.querySelector('#media-input');
            const mediaContainer = document.querySelector('#media-container');

            mediaInput.addEventListener('change', function(e) {
                const files = e.target.files;

                // Remove previous previews, if any
                const existingPreviews = document.querySelectorAll('.media-preview');
                existingPreviews.forEach(preview => preview.remove());

                // Create previews for new files
                Array.from(files).forEach(file => {
                    const reader = new FileReader();
                    const previewDiv = document.createElement('div');
                    previewDiv.classList.add('relative', 'group', 'w-40', 'media-preview');

                    reader.onload = function(e) {
                        if (file.type.startsWith('image/')) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.classList.add('w-full', 'h-32', 'object-cover', 'rounded-md',
                                'border');
                            previewDiv.appendChild(img);
                        } else if (file.type.startsWith('video/')) {
                            const video = document.createElement('video');
                            video.src = e.target.result;
                            video.controls = true;
                            video.classList.add('w-full', 'h-32', 'object-cover', 'rounded-md',
                                'border');
                            previewDiv.appendChild(video);
                        }
                    };

                    reader.readAsDataURL(file);
                    mediaContainer.insertBefore(previewDiv, mediaContainer.lastElementChild);
                });
            });
        });
    </script>
@endsection