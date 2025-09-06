@extends('layouts.app')

@section('title', 'Danh sách bài viết')

@section('content')
    <style>
        /* CSS for lazy loading and smooth transitions */
        .post-article {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }

        .post-article.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .post-image {
            opacity: 0;
            transition: opacity 0.5s ease;
            border: 1px solid rgba(0, 0, 0, 0.2);
            /* viền đen mờ */
            border-radius: 8px;
            /* bo góc nhẹ */
            display: block;
            max-height: 250px;
            /* giới hạn chiều cao ảnh */
            object-fit: cover;
        }

        .post-image.visible {
            opacity: 1;
        }

        .post-images {
            display: inline-flex;
            /* để gallery co theo số ảnh */
            background-color: #f3f3f3;
            /* nền xám */
            padding: 8px;
            border-radius: 10px;
            gap: 8px;
            /* khoảng cách giữa các ảnh */
            max-width: 100%;
            /* không tràn khỏi bài viết */
            overflow-x: auto;
            /* cuộn ngang nếu nhiều ảnh */
        }

        .image-box {
            flex: 0 0 auto;
            /* mỗi ảnh giữ kích thước riêng */
        }
    </style>

    <header class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">Danh sách bài viết</h1>
        <a href="{{ route('posts.create') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-md">Đăng bài</a>
    </header>

    @foreach ($posts as $post)
        <article class="mb-6 bg-white rounded-2xl shadow-sm p-4 post-article" data-id="{{ $post->id }}">
            <div class="flex items-start gap-4">
                {{-- Avatar --}}
                <div class="flex-shrink-0">
                    <img src="{{ $post->user->avatar ?? asset('images/default-avatar.png') }}" alt="{{ $post->user->name }}"
                        class="h-12 w-12 rounded-full object-cover">
                </div>

                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold">{{ $post->title }}</h2>
                            <div class="text-sm text-gray-500">
                                Bởi <strong>{{ $post->user->name }}</strong> ·
                                @if ($post->created_at->gt(now()->subDays(3)))
                                    {{ $post->created_at->diffForHumans() }}
                                @else
                                    {{ $post->created_at->format('d/m/Y') }}
                                @endif
                            </div>

                        </div>
                        <a href="{{ route('posts.show', $post) }}" class="text-sm text-blue-600">Xem chi tiết</a>
                    </div>

                    {{-- Content --}}
                    <p class="mt-3 text-sm text-gray-800 line-clamp-3">{!! nl2br(e($post->content)) !!}</p>

                    {{-- Hashtags --}}
                    @if (!empty($post->hashtag))
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach (explode(',', $post->hashtag) as $tag)
                                <span
                                    class="text-xs px-2 py-1 bg-gray-100 rounded-full text-gray-600">{{ trim($tag) }}</span>
                            @endforeach
                        </div>
                    @endif

                    {{-- Images gallery --}}
                    @if ($post->images && $post->images->count())
                        <div class="mt-4">
                            <div class="post-images">
                                @foreach ($post->images as $img)
                                    <div class="image-box mr-2">
                                        <img data-src="{{ asset('storage/' . $img->image_path) }}" alt="Ảnh bài viết"
                                            class="post-image" loading="lazy">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Actions (like/share/comment) --}}
                    <div class="mt-4 flex items-center gap-4 text-sm text-gray-600">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                            </svg>
                            <span>{{ $post->likes ?? 0 }}</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8s-9-3.582-9-8 4.03-8 9-8 9 3.582 9 8z" />
                            </svg>
                            <span>{{ $post->shares ?? 0 }}</span>
                        </div>

                        <div class="ml-auto text-gray-400">{{ $post->views ?? 0 }} lượt xem</div>
                    </div>
                </div>
            </div>
        </article>
    @endforeach

    <script>
        // JavaScript for lazy loading posts and images using Intersection Observer
        document.addEventListener('DOMContentLoaded', () => {
            const articles = document.querySelectorAll('.post-article');
            const images = document.querySelectorAll('.post-image');

            // Observer for articles
            const articleObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                rootMargin: '0px 0px 100px 0px', // Trigger 100px before the element is visible
                threshold: 0.1
            });

            // Observer for images
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src; // Load the image by setting src
                        img.classList.add('visible');
                        observer.unobserve(img);
                    }
                });
            }, {
                rootMargin: '0px 0px 100px 0px',
                threshold: 0.1
            });

            // Observe all articles and images
            articles.forEach(article => articleObserver.observe(article));
            images.forEach(image => imageObserver.observe(image));
        });
    </script>
@endsection
