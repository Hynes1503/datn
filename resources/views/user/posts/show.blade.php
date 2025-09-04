@extends('layouts.app')

@section('title', $post->title)

@section('content')
  <article class="mb-6 bg-white rounded-2xl shadow-sm p-4">
    <div class="flex items-start gap-4">
      {{-- Avatar --}}
      <div class="flex-shrink-0">
        <img src="{{ $post->user->avatar ?? asset('images/default-avatar.png') }}" alt="{{ $post->user->name }}" class="h-12 w-12 rounded-full object-cover">
      </div>

      <div class="flex-1">
        <div class="flex items-center justify-between relative">
          <div>
            <h2 class="text-lg font-semibold">{{ $post->title }}</h2>
            <div class="text-sm text-gray-500">Bởi <strong>{{ $post->user->name }}</strong> · {{ $post->created_at->diffForHumans() }}</div>
          </div>

          {{-- Options Button (visible only to post author) --}}
          @if(auth()->check() && auth()->user()->id === $post->user->id)
            <div>
              <button class="text-gray-600 hover:text-gray-900 focus:outline-none" onclick="toggleOptionsMenu('options-{{ $post->id }}')">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v.01M12 12v.01M12 18v.01"/>
                </svg>
              </button>
              <div id="options-{{ $post->id }}" class="hidden absolute right-0 mt-2 w-32 bg-white rounded-md shadow-lg z-10">
                <a href="{{ route('posts.edit', $post) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sửa</a>
                <form action="{{ route('posts.destroy', $post) }}" method="POST">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Xóa</button>
                </form>
              </div>
            </div>
          @endif
        </div>

        {{-- Content --}}
        <p class="mt-3 text-sm text-gray-800">{!! nl2br(e($post->content)) !!}</p>

        {{-- Hashtags --}}
        @if($post->hashtag)
          <div class="mt-3 flex flex-wrap gap-2">
            @foreach(explode(',', $post->hashtag) as $tag)
              <span class="text-xs px-2 py-1 bg-gray-100 rounded-full text-gray-600">#{{ trim($tag) }}</span>
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

        {{-- Actions (like only) --}}
        <div class="mt-4 flex items-center gap-4 text-sm text-gray-600">
          <form action="#" method="POST">
            @csrf
            <button type="submit" class="flex items-center gap-2 hover:text-blue-600">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
              </svg>
              <span>{{ $post->likes ?? 0 }}</span>
            </button>
          </form>

          <div class="ml-auto text-gray-400">{{ $post->views ?? 0 }} lượt xem</div>
        </div>

        {{-- Likes Details --}}
        @if($post->likes > 0)
          <div class="mt-4">
            <h3 class="text-sm font-semibold text-gray-700">Lượt thích ({{ $post->likes }})</h3>
            <p class="text-sm text-gray-600">Bài viết được thích bởi {{ $post->likes }} người.</p>
          </div>
        @endif
      </div>
    </div>
  </article>

  <script>
    function toggleOptionsMenu(menuId) {
      const menu = document.getElementById(menuId);
      menu.classList.toggle('hidden');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
      const menus = document.querySelectorAll('[id^="options-"]');
      menus.forEach(menu => {
        const button = menu.previousElementSibling;
        if (!menu.contains(event.target) && !button.contains(event.target)) {
          menu.classList.add('hidden');
        }
      });
    });
  </script>
@endsection