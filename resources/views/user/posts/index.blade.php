@extends('layouts.app')

@section('title', 'Danh sách bài viết')

@section('content')
  <header class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold">Danh sách bài viết</h1>
    <a href="{{ route('posts.create') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-md">Đăng bài</a>
  </header>

  @foreach ($posts as $post)
    <article class="mb-6 bg-white rounded-2xl shadow-sm p-4">
      <div class="flex items-start gap-4">
        {{-- Avatar --}}
        <div class="flex-shrink-0">
          <img src="{{ $post->user->avatar ?? asset('images/default-avatar.png') }}" alt="{{ $post->user->name }}" class="h-12 w-12 rounded-full object-cover">
        </div>

        <div class="flex-1">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-lg font-semibold">{{ $post->title }}</h2>
              <div class="text-sm text-gray-500">Bởi <strong>{{ $post->user->name }}</strong> · {{ $post->created_at->diffForHumans() }}</div>
            </div>
            <a href="{{ route('posts.show', $post) }}" class="text-sm text-blue-600">Xem chi tiết</a>
          </div>

          {{-- Content --}}
          <p class="mt-3 text-sm text-gray-800 line-clamp-3">{!! nl2br(e($post->content)) !!}</p>

          {{-- Hashtags --}}
          @if(!empty($post->hashtag))
            <div class="mt-3 flex flex-wrap gap-2">
              @foreach(explode(',', $post->hashtag) as $tag)
                <span class="text-xs px-2 py-1 bg-gray-100 rounded-full text-gray-600">{{ trim($tag) }}</span>
              @endforeach
            </div>
          @endif

          {{-- Images gallery --}}
          @if($post->images && $post->images->count())
            <div class="mt-4">
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

          {{-- Actions (like/share/comment) --}}
          <div class="mt-4 flex items-center gap-4 text-sm text-gray-600">
            <div class="flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
              <span>{{ $post->likes ?? 0 }}</span>
            </div>

            <div class="flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8s-9-3.582-9-8 4.03-8 9-8 9 3.582 9 8z"/></svg>
              <span>{{ $post->shares ?? 0 }}</span>
            </div>

            <div class="ml-auto text-gray-400">{{ $post->views ?? 0 }} lượt xem</div>
          </div>
        </div>
      </div>
    </article>
  @endforeach

  <div class="mt-6">
    {{ $posts->links() }}
  </div>
@endsection