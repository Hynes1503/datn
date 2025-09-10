@extends('layouts.app')

@section('title', 'Bài viết với hashtag #' . $hashtag)
@section('menuTitle', 'Bài viết với hashtag #' . $hashtag)

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

        /* CSS for dropdown menu */
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
            font-size: 1.25rem;
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

        /* Tim bay */
        .floating-heart {
            position: absolute;
            font-size: 1.2rem;
            color: #ef4444; /* đỏ-500 */
            animation: floatUp 1s ease-out forwards;
            pointer-events: none;
        }

        @keyframes floatUp {
            0% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
            50% {
                opacity: 0.8;
                transform: translateY(-30px) scale(1.3);
            }
            100% {
                opacity: 0;
                transform: translateY(-60px) scale(0.8);
            }
        }

        /* Ensure active hashtag doesn't change on hover */
        .bg-black:hover {
            background-color: #000 !important;
        }
    </style>

    @if (auth()->check())
        <a href="{{ route('posts.create') }}" id="openPostModal" class="post-creation-form text-gray-400 text-base">
            <img src="{{ asset('storage/' . (Auth::user()->avatar ?? '')) }}" alt="{{ Auth::user()->name }}"
                class="h-10 w-10 rounded-full object-cover"
                onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.png') }}';">
            <span><i class="fa-solid fa-pen"></i> Có gì thú vị không?</span>
        </a>
    @endif

    @if ($posts->isEmpty())
        <p class="text-gray-500 text-center">Không có bài viết nào với hashtag #{{ $hashtag }}.</p>
    @else
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

                            <!-- Dropdown for edit/delete options -->
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

                        <!-- Content -->
                        <p class="mt-3 text-sm text-gray-800 line-clamp-3">{!! nl2br(e($post->content)) !!}</p>

                        <!-- Hashtags -->
                        @if (!empty($post->hashtag))
                            <div class="mt-3 flex flex-wrap gap-2">
                                @php
                                    // Tách hashtag từ cột hashtag, loại bỏ ký tự # và chuẩn hóa
                                    $tags = array_map('trim', explode(',', str_replace('#', '', $post->hashtag)));
                                    // Lấy danh sách hashtag hiện tại từ URL
                                    $currentHashtags = array_map('trim', explode(',', $hashtag));
                                    // Loại bỏ trùng lặp và chuẩn hóa
                                    $currentHashtags = array_unique(array_map('strtolower', $currentHashtags));
                                    // Tách hashtag thành hai nhóm: đang chọn và không chọn
                                    $selectedTags = [];
                                    $otherTags = [];
                                    foreach ($tags as $tag) {
                                        if (in_array(strtolower($tag), $currentHashtags)) {
                                            $selectedTags[] = $tag;
                                        } else {
                                            $otherTags[] = $tag;
                                        }
                                    }
                                    // Kết hợp danh sách: hashtag đang chọn trước, sau đó đến hashtag không chọn
                                    $sortedTags = array_merge($selectedTags, $otherTags);
                                @endphp
                                @foreach ($sortedTags as $tag)
                                    @php
                                        // Kiểm tra xem hashtag có trong danh sách hiện tại không
                                        $isCurrentTag = in_array(strtolower($tag), $currentHashtags);
                                        // Tạo danh sách hashtag mới
                                        if ($isCurrentTag) {
                                            // Loại bỏ hashtag được nhấp khỏi danh sách
                                            $newHashtagList = implode(',', array_diff($currentHashtags, [strtolower($tag)]));
                                        } else {
                                            // Thêm hashtag mới vào danh sách
                                            $newHashtagList = $hashtag ? $hashtag . ',' . $tag : $tag;
                                        }
                                        // Nếu danh sách rỗng, chuyển về trang mặc định
                                        $newHashtagList = $newHashtagList ?: 'empty';
                                    @endphp
                                    <a href="{{ route('posts.byHashtag', $newHashtagList) }}"
                                       class="text-xs px-2 py-1 rounded-full {{ $isCurrentTag ? 'bg-black text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                        #{{ $tag }}
                                    </a>
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
                                @include('layouts.like', ['post' => $post])
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

        <!-- Pagination -->
        {{ $posts->links() }}
    @endif

    <script>
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

            // Like button with floating heart animation using event delegation
            let isProcessing = false; // Prevent multiple rapid clicks

            document.addEventListener('submit', async function(event) {
                const form = event.target.closest('.like-form');
                if (!form || isProcessing) return; // Skip if not a like form or processing
                event.preventDefault();
                isProcessing = true;

                const button = form.querySelector('button');
                const likeIcon = button.querySelector('.like-icon');
                const likeCount = form.querySelector('.like-count');
                const url = form.getAttribute('action');
                const token = form.querySelector('input[name="_token"]').value;

                let count = parseInt(likeCount.textContent);
                const isLiked = likeIcon.classList.contains('fa-solid');

                // Optimistic UI update
                if (isLiked) {
                    likeIcon.classList.remove('fa-solid', 'text-red-500');
                    likeIcon.classList.add('fa-regular', 'text-gray-500');
                    likeCount.textContent = count - 1;
                } else {
                    likeIcon.classList.remove('fa-regular', 'text-gray-500');
                    likeIcon.classList.add('fa-solid', 'text-red-500');
                    likeCount.textContent = count + 1;

                    // Create floating heart animation
                    const heart = document.createElement('span');
                    heart.innerHTML = '<i class="fa-solid fa-heart"></i>';
                    heart.classList.add('floating-heart');
                    const rect = button.getBoundingClientRect();
                    heart.style.left = rect.width / 2 + 'px';
                    heart.style.top = '-10px';
                    button.style.position = 'relative';
                    button.appendChild(heart);
                    setTimeout(() => heart.remove(), 1000);
                }

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({})
                    });

                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        throw new Error(data.error || 'Lỗi không xác định');
                    }

                    // Sync with server
                    likeCount.textContent = data.likes_count;
                } catch (error) {
                    console.error('Lỗi fetch:', error);
                    alert('Có lỗi xảy ra, vui lòng thử lại.');

                    // Rollback UI on error
                    if (isLiked) {
                        likeIcon.classList.remove('fa-regular', 'text-gray-500');
                        likeIcon.classList.add('fa-solid', 'text-red-500');
                        likeCount.textContent = count;
                    } else {
                        likeIcon.classList.remove('fa-solid', 'text-red-500');
                        likeIcon.classList.add('fa-regular', 'text-gray-500');
                        likeCount.textContent = count;
                    }
                } finally {
                    isProcessing = false; // Reset processing flag
                }
            });

            // Dropdown toggle handler
            document.querySelectorAll('.dropdown-toggle').forEach(button => {
                button.addEventListener('click', () => {
                    const postId = button.getAttribute('data-post-id');
                    const dropdownMenu = document.getElementById(`dropdown-menu-${postId}`);
                    dropdownMenu.classList.toggle('show');

                    // Close other open dropdowns
                    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                        if (menu.id !== `dropdown-menu-${postId}`) {
                            menu.classList.remove('show');
                        }
                    });
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
        });
    </script>
@endsection