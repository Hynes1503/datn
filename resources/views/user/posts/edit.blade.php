@extends('layouts.app')

@section('title', 'Sửa bài viết')

@section('content')
  <div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-6">
      <h2 class="text-2xl font-bold mb-6">Sửa bài viết</h2>
      <form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        {{-- Title --}}
        <div class="mb-4">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Tiêu đề</label>
          <input type="text" name="title" class="w-full p-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-600" value="{{ old('title', $post->title) }}" required>
          @error('title')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Content --}}
        <div class="mb-4">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Nội dung</label>
          <textarea name="content" class="w-full p-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-600" rows="5" required>{{ old('content', $post->content) }}</textarea>
          @error('content')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Hashtags --}}
        <div class="mb-4">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Hashtag</label>
          <input type="text" name="hashtag" class="w-full p-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-600" value="{{ old('hashtag', $post->hashtag) }}" placeholder="Nhập hashtag, cách nhau bằng dấu phẩy">
          @error('hashtag')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Current Images --}}
        @if($post->images && $post->images->count())
          <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Ảnh hiện tại</label>
            <div class="post-images fade-right">
              @foreach($post->images as $img)
                <div class="image-box mr-2">
                  <img
                    src="{{ asset('storage/' . $img->image_path) }}"
                    alt="Ảnh bài viết"
                    class="post-image"
                    loading="lazy"
                  >
                </div>
              @endforeach
            </div>
          </div>
        @endif

        {{-- New Images Upload --}}
        <div class="mb-4">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Thêm ảnh mới</label>
          <input type="file" name="image[]" class="w-full p-2 border border-gray-300 rounded-md text-sm" multiple>
          @error('image.*')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Submit Button --}}
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">Cập nhật</button>
      </form>
    </div>
  </div>
@endsection