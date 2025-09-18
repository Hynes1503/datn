@extends('admin.layouts.app')

@section('title', 'View Post')
@section('page-title', 'Post Details')

@section('content')
<style>
    /* CSS for media gallery */
    .post-images {
        display: inline-flex;
        gap: 8px;
        max-width: 100%;
        overflow-x: auto;
    }

    .image-box {
        flex: 0 0 auto;
        background-color: #f3f3f3;
        border-radius: 10px;
        padding: 4px;
    }

    .post-image {
        border: 1px solid rgba(0, 0, 0, 0.2);
        border-radius: 8px;
        max-height: 150px;
        object-fit: cover;
    }

    .post-video {
        border: 1px solid rgba(0, 0, 0, 0.2);
        border-radius: 8px;
        max-height: 150px;
        object-fit: cover;
    }
</style>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Post Details</h2>
        <div class="flex space-x-2">
            <a href="{{ route('admin.posts.edit', $post->slug) }}" 
               class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg transition duration-200">
                <i class="fa-solid fa-pen-to-square mr-1"></i> Edit Post
            </a>
            <a href="{{ route('admin.posts.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                <i class="fa-solid fa-arrow-left mr-1"></i> Back to Posts
            </a>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6 border-l-4 border-black">
        <div class="flex items-start space-x-4">
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900">{{ $post->title }}</h3>
                <p class="mt-2 text-gray-600">{!! nl2br(e($post->content)) !!}</p>
                <div class="mt-3 flex space-x-4 text-sm text-gray-500">
                    <span>Hashtags: {{ $post->hashtag ?? 'None' }}</span>
                    <span>Likes: {{ $post->likes_count }}</span>
                    <span>Shares: {{ $post->shares }}</span>
                    <span>Views: {{ $post->views }}</span>
                </div>
                <div class="mt-2 text-sm text-gray-500">
                    Created: {{ $post->created_at->format('d/m/Y') }} ({{ $post->created_at->diffForHumans() }})
                </div>
                @if ($post->room)
                    <div class="mt-2 text-sm text-gray-500">
                        Room: {{ $post->room->name ?? 'N/A' }}
                    </div>
                @endif
                @if ($post->building)
                    <div class="mt-1 text-sm text-gray-500">
                        Building: {{ $post->building->name ?? 'N/A' }}
                    </div>
                @endif
            </div>
        </div>
        @if ($post->media && $post->media->count())
            <div class="mt-4">
                <div class="post-images">
                    @foreach ($post->media as $m)
                        <div class="image-box">
                            @if ($m->media_type === 'image')
                                <img src="{{ asset('storage/' . $m->media_path) }}" alt="Ảnh bài viết"
                                     class="post-image" loading="lazy">
                            @elseif ($m->media_type === 'video')
                                <video controls class="post-video">
                                    <source src="{{ asset('storage/' . $m->media_path) }}" type="video/mp4">
                                    Trình duyệt không hỗ trợ video.
                                </video>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection