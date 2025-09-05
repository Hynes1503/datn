@extends('layouts.app')

@section('title', $user->name . ' (@' . $user->mention . ')')

@section('content')
    <header class="mb-6 bg-white rounded-2xl shadow-sm p-6 flex items-center gap-6">
        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-24 w-24 rounded-full object-cover">
        <div class="flex-1">
            <h1 class="text-2xl font-bold">{{ $user->name }}</h1>
            <p class="text-gray-600">@_{{ $user->mention }}</p>

            {{-- Hiển thị thông tin theo visibility --}}
            @if ($user->profile_visibility['dob'] ?? false)
                <p class="text-sm text-gray-500">Ngày sinh: {{ $user->dob?->format('d/m/Y') }}</p>
            @endif

            @if ($user->profile_visibility['class'] ?? false)
                <p class="text-sm text-gray-500">Lớp: {{ $user->class }}</p>
            @endif

            @if ($user->profile_visibility['major'] ?? false)
                <p class="text-sm text-gray-500">Ngành: {{ $user->major }}</p>
            @endif

            @if ($user->profile_visibility['course'] ?? false)
                <p class="text-sm text-gray-500">Khóa: {{ $user->course }}</p>
            @endif

            @if ($user->profile_visibility['student_id'] ?? false)
                <p class="text-sm text-gray-500">MSSV: {{ $user->student_id }}</p>
            @endif
        </div>

        {{-- Nút chỉnh sửa hồ sơ nếu là chủ tài khoản --}}
        @if (auth()->check() && auth()->id() === $user->id)
            <div>
                <a href="{{ route('users.edit', $user) }}"
                    class="p-3 border border-gray-300 rounded-full text-gray-700 hover:bg-gray-100 transition text-xl">
                    <i class="fa-solid fa-user-pen"></i>
                </a>
            </div>
        @endif
    </header>

    {{-- Danh sách bài viết --}}
    <h2 class="text-xl font-semibold mb-4">Bài viết của {{ $user->name }}</h2>

    @if ($user->posts->count())
        @foreach ($user->posts as $post)
            <a href="{{ route('posts.show', $post) }}">
                <article class="mb-6 bg-white rounded-2xl shadow-sm p-4 post-article">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <img src="{{ $post->user->avatar ?? asset('images/default-avatar.png') }}"
                                alt="{{ $post->user->name }}" class="h-12 w-12 rounded-full object-cover">
                        </div>
                        <div class="flex-1">
                            <h2 class="text-lg font-semibold">{{ $post->title }}</h2>
                            <div class="text-sm text-gray-500">
                                {{ $post->created_at->format('d/m/Y') }} · {{ $post->created_at->diffForHumans() }}
                            </div>
                            <p class="mt-3 text-sm text-gray-800 line-clamp-3">{!! nl2br(e($post->content)) !!}</p>

                            @if (!empty($post->hashtag))
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach (explode(',', $post->hashtag) as $tag)
                                        <span class="text-xs px-2 py-1 bg-gray-100 rounded-full text-gray-600">
                                            {{ trim($tag) }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            @if ($post->images && $post->images->count())
                                <div class="mt-4">
                                    <div class="post-images fade-right">
                                        @foreach ($post->images as $img)
                                            <div class="image-box mr-2">
                                                <img src="{{ asset('storage/' . $img->image_path) }}" alt="Ảnh bài viết"
                                                    class="post-image" loading="lazy">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="mt-4 flex items-center gap-4 text-sm text-gray-600">
                                👍 <span>{{ $post->likes ?? 0 }}</span>
                                🔄 <span>{{ $post->shares ?? 0 }}</span>
                                <span class="ml-auto text-gray-400">{{ $post->views ?? 0 }} lượt xem</span>
                            </div>
                        </div>
                    </div>
                </article>
            </a>
        @endforeach
    @else
        @if (auth()->check() && auth()->id() === $user->id)
            <div class="bg-white rounded-2xl shadow-sm p-8 flex flex-col items-center justify-center text-center">
                <div class="w-16 h-16 flex items-center justify-center rounded-full bg-gray-100 mb-4">
                    <i class="fa-solid fa-pen text-2xl text-gray-500"></i>
                </div>
                <h3 class="text-lg font-semibold mb-2">Chia sẻ suy nghĩ đầu tiên của bạn</h3>
                <p class="text-gray-500 mb-6 text-sm">Bắt đầu viết bài để mọi người có thể thấy bạn.</p>
                <a href="{{ route('posts.create') }}"
                    class="px-6 py-2 bg-black text-white font-medium rounded-full hover:bg-gray-800 transition">
                    Viết bài
                </a>
            </div>
        @else
            <p class="text-gray-500">Người dùng này chưa có bài viết nào.</p>
        @endif
    @endif

@endsection
