@extends('layouts.app')

@section('title', $user->name . ' (@' . $user->mention . ')')
@section('menuTitle', 'Trang cá nhân')

@section('content')
    <style>
        /* CSS for lazy loading and smooth transitions */
        .post-article {
            opacity: 0;
            transform: translateY(20px);
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
            display: block;
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

        /* End of posts notification */
        .end-of-posts {
            display: none;
            width: 100%;
            text-align: center;
            padding: 16px;
            background-color: #f3f3f3;
            border-top: 1px solid #e5e7eb;
            border-radius: 0 0 8px 8px;
            color: #6b7280;
            font-size: 14px;
            margin-top: 16px;
        }

        .end-of-posts.visible {
            display: block;
        }

        /* Horizontal line for end of posts */
        .end-of-posts-line {
            display: none;
            border: 0;
            height: 1px;
            background-color: #e5e7eb;
            margin: 16px 0;
        }

        .end-of-posts-line.visible {
            display: block;
        }

        /* Dropdown styles */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-toggle {
            padding: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 9999px;
            color: #4b5563;
            transition: background-color 0.2s;
            font-size: 1.25rem; /* text-xl */
        }

        .dropdown-toggle:hover {
            background-color: #f3f3f3;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background-color: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            min-width: 120px;
            z-index: 10;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-menu a,
        .dropdown-menu button {
            display: block;
            padding: 8px 16px;
            color: #4b5563;
            text-decoration: none;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }

        .dropdown-menu a:hover,
        .dropdown-menu button:hover {
            background-color: #f3f3f3;
        }

        .dropdown-menu button.text-red-500:hover {
            background-color: #fee2e2;
        }
    </style>

    <header class="mb-6 bg-white rounded-2xl shadow-sm p-6 flex items-center gap-6">
        <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.png') }}"
            alt="{{ $user->name }}" class="h-24 w-24 rounded-full object-cover">
        <div class="flex-1">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">{{ $user->name }}</h1>
                    <p class="text-gray-600">{{ "@$user->mention" }}</p>
                </div>

                <div class="flex items-center gap-4">
                    @if (auth()->check() && auth()->id() === $user->id)
                        <div>
                            <a href="{{ route('users.edit', $user) }}"
                                class="p-3 border border-gray-300 rounded-full text-gray-700 hover:bg-gray-100 transition text-xl">
                                <i class="fa-solid fa-user-pen"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-3 text-sm text-gray-500">
                @if ($user->profile_visibility['dob'] ?? false)
                    <p>Ngày sinh: {{ $user->dob?->format('d/m/Y') }}</p>
                @endif
                @if ($user->profile_visibility['class'] ?? false)
                    <p>Lớp: {{ $user->class }}</p>
                @endif
                @if ($user->profile_visibility['major'] ?? false)
                    <p>Ngành: {{ $user->major }}</p>
                @endif
                @if ($user->profile_visibility['course'] ?? false)
                    <p>Khóa: {{ $user->course }}</p>
                @endif
                @if ($user->profile_visibility['student_id'] ?? false)
                    <p>MSSV: {{ $user->student_id }}</p>
                @endif
            </div>
        </div>
    </header>

    <header class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold">Bài viết của {{ $user->name }}</h2>
    </header>

    @if ($user->posts->count())
        @foreach ($user->posts as $post)
            <article class="mb-6 bg-white rounded-2xl shadow-sm p-4 post-article" data-id="{{ $post->id }}">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.png') }}"
                            alt="{{ $post->user->name }}" class="h-12 w-12 rounded-full object-cover">
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold">{{ $post->title }}</h2>
                                <div class="text-sm text-gray-500">
                                    Bởi <strong>{{ $post->user->name }}</strong>
                                    <div>
                                        {{ $post->created_at->format('d/m/Y') }} ·
                                        {{ $post->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                            @if (auth()->check() && auth()->user()->id === $post->user_id)
                                <div class="dropdown">
                                    <button class="dropdown-toggle" type="button" data-post-id="{{ $post->id }}">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu" id="dropdown-menu-{{ $post->id }}">
                                        <a href="{{ route('posts.edit', [$post->user->mention, $post->slug]) }}">Sửa</a>
                                        <form action="{{ route('posts.destroy', [$post->user->mention, $post->slug]) }}"
                                            method="POST" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500"
                                                onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này?');">Xóa</button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <p class="mt-3 text-sm text-gray-800 line-clamp-3">{!! nl2br(e($post->content)) !!}</p>

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
                                                    <source src="{{ asset('storage/' . $m->media_path) }}"
                                                        type="video/mp4">
                                                    Trình duyệt không hỗ trợ video.
                                                </video>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="mt-4 flex items-center gap-4 text-sm text-gray-600">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 15l7-7 7 7" />
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
        <!-- Horizontal line for end of posts -->
        <hr class="end-of-posts-line" id="endOfPostsLine">
        <!-- End of posts notification -->
        <div class="end-of-posts" id="endOfPosts">Bạn đã xem hết bài đăng của người này</div>
    @else
        @if (auth()->check() && auth()->id() === $user->id)
            <div class="bg-white rounded-2xl shadow-sm p-8 flex flex-col items-center justify-center text-center">
                <div class="w-16 h-16 flex items-center justify-center rounded-full bg-gray-100 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.536L16.732 3.732z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold mb-2">Chia sẻ bài viết đầu tiên của bạn</h3>
                <p class="text-gray-500 mb-6 text-sm">Bắt đầu đăng bài để mọi người biết đến bạn.</p>
                <a href="#"
                    class="px-6 py-2 bg-black text-white font-medium rounded-full hover:bg-gray-800 transition open-post-modal">
                    Viết bài
                </a>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm p-8 text-center">
                <p class="text-gray-500">Người dùng này chưa có bài viết nào.</p>
            </div>
        @endif
    @endif

    <script>
        // JavaScript for lazy loading posts and images using Intersection Observer
        document.addEventListener('DOMContentLoaded', () => {
            // Dropdown toggle functionality
            const dropdownToggles = document.querySelectorAll('.dropdown-toggle');

            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', (e) => {
                    e.preventDefault();
                    const dropdownMenu = toggle.nextElementSibling;
                    const isShown = dropdownMenu.classList.contains('show');

                    // Close all other dropdowns
                    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                        if (menu !== dropdownMenu) {
                            menu.classList.remove('show');
                        }
                    });

                    // Toggle the current dropdown
                    dropdownMenu.classList.toggle('show', !isShown);
                });
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.dropdown')) {
                    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                        menu.classList.remove('show');
                    });
                }
            });

            // Lazy loading for articles
            const articles = document.querySelectorAll('.post-article');
            const images = document.querySelectorAll('.post-image');
            const endOfPosts = document.getElementById('endOfPosts');
            const endOfPostsLine = document.getElementById('endOfPostsLine');

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

            // Observer for end of posts and horizontal line
            const endObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        endOfPosts.classList.add('visible');
                        endOfPostsLine.classList.add('visible');
                    }
                });
            }, {
                rootMargin: '0px 0px -50px 0px', // Trigger when 50px from the bottom
                threshold: 0.1
            });

            // Observe all articles, images, end of posts, and horizontal line
            articles.forEach(article => articleObserver.observe(article));
            images.forEach(image => imageObserver.observe(image));
            if (endOfPosts && endOfPostsLine) {
                endObserver.observe(endOfPosts);
            }
        });
    </script>
@endsection