@extends('layouts.app')

@section('title', 'Trang chủ')
@section('menuTitle', 'Sự kiện mới')

@section('content')
    <style>
        /* CSS for post creation form */
        .post-creation-form {
            background-color: #fff;
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .post-creation-form img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* CSS for lazy loading posts and images */
        .post-article {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
            text-decoration: none;
            display: block;
            background-color: #fff;
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
        }

        .post-article.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .post-image {
            opacity: 0;
            transition: opacity 0.5s ease;
            border: 1px solid rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            max-height: 250px;
            object-fit: cover;
        }

        .post-image.visible {
            opacity: 1;
        }

        .post-images {
            display: inline-flex;
            background-color: #f3f3f3;
            padding: 8px;
            border-radius: 10px;
            gap: 8px;
            max-width: 100%;
            overflow-x: auto;
        }

        .image-box {
            flex: 0 0 auto;
        }
    </style>

    @if (auth()->check())
        <a href="#" id="openPostModal" class="post-creation-form text-gray-400 text-base">
            <img src="{{ asset('storage/' . (Auth::user()->avatar ?? '')) }}" alt="{{ Auth::user()->name }}"
                class="h-10 w-10 rounded-full object-cover"
                onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.png') }}';">
            <span>Bạn đang nghĩ gì?</span>
        </a>
    @endif

    @foreach ($posts as $post)
        <article class="post-article" data-id="{{ $post->id }}">
            <div class="flex items-start gap-4">
                <!-- Avatar -->
                <div class="flex-shrink-0">
                    <a href="{{ route('users.show', $post->user->mention) }}">
                        <img src="{{ asset('storage/' . ($post->user->avatar ?? '')) }}" alt="{{ $post->user->name }}"
                            class="h-24 w-24 rounded-full object-cover"
                            onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.png') }}';">
                    </a>
                </div>

                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <a href="{{ route('posts.show', [$post->user->mention, $post->slug]) }}"
                                class="hover:underline">
                                <h2 class="text-lg font-semibold">{{ $post->title }}</h2>
                            </a>

                            <div class="text-sm text-gray-500">
                                Bởi <strong>{{ $post->user->name }}</strong>
                                <div>
                                    {{ $post->created_at->format('d/m/Y') }} ·
                                    {{ $post->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <p class="mt-3 text-sm text-gray-800 line-clamp-3">{!! nl2br(e($post->content)) !!}</p>

                    <!-- Hashtags -->
                    @if (!empty($post->hashtag))
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach (explode(',', $post->hashtag) as $tag)
                                <span
                                    class="text-xs px-2 py-1 bg-gray-100 rounded-full text-gray-600">{{ trim($tag) }}</span>
                            @endforeach
                        </div>
                    @endif

                    <!-- Media gallery -->
                    @if ($post->media && $post->media->count())
                        <div class="mt-4">
                            <div class="post-images">
                                @foreach ($post->media as $m)
                                    <div class="image-box mr-2">
                                        @if ($m->media_type === 'image')
                                            <img data-src="{{ asset('storage/' . $m->media_path) }}" alt="Ảnh bài viết"
                                                class="post-image" loading="lazy">
                                        @elseif ($m->media_type === 'video')
                                            <video controls class="rounded-lg max-h-60">
                                                <source src="{{ asset('storage/' . $m->media_path) }}" type="video/mp4">
                                                Trình duyệt không hỗ trợ video.
                                            </video>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <!-- Actions (like/share/comment) -->
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
        // JavaScript for lazy loading posts and images
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
                rootMargin: '0px 0px 100px 0px',
                threshold: 0.1
            });

            // Observer for images
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
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
