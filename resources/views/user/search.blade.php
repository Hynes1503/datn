@extends('layouts.app')

@section('title', 'Tìm kiếm')
@section('menuTitle', 'Tìm kiếm')

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
            padding: 6px;
            border: 1px solid #e5e7eb;
            border-radius: 9999px;
            color: #4b5563;
            transition: background-color 0.2s;
            font-size: 0.75rem;
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
            color: #ef4444;
            /* đỏ-500 */
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

        /* Tab styling */
        .tab-link {
            display: inline-block;
            padding: 8px 12px;
            border-bottom: 2px solid transparent;
            cursor: pointer;
            color: #4b5563;
            font-weight: 500;
            transition: all 0.2s;
        }

        .tab-link:hover {
            color: #3b82f6;
        }

        .tab-link.active {
            border-bottom: 2px solid #3b82f6;
            font-weight: bold;
            color: #3b82f6;
        }

        /* User card styling */
        .user-card {
            background-color: #fff;
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
        }
    </style>

    <div class="max-w-3xl mx-auto mt-6">


        <h2 class="text-xl font-bold mb-4">Kết quả cho: "{{ $query }}"</h2>

        <div>
            <ul class="flex border-b mb-4">
                <li class="mr-6">
                    <a href="#users" class="tab-link active" onclick="showTab('users')">Người dùng</a>
                </li>
                <li class="mr-6">
                    <a href="#posts" class="tab-link" onclick="showTab('posts')">Bài viết</a>
                </li>
            </ul>
        </div>

        <!-- Người dùng -->
        <div id="users" class="tab-content mt-4">
            @forelse ($users as $user)
                <div class="user-card flex items-center gap-3">
                    <img src="{{ $user->avatar_url }}" alt="avatar" class="w-10 h-10 rounded-full object-cover"
                        onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.png') }}';">
                    <div>
                        <a href="{{ route('users.show', $user->mention) }}" class="font-semibold hover:underline">
                            {{ $user->name }}
                        </a>
                        <p class="text-sm text-gray-500">{{ '@' . $user->mention }}</p>
                        <div class="mt-2 text-sm text-gray-500">
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
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow-sm p-8 text-center">
                    <p class="text-gray-500 text-center">Không tìm thấy người dùng nào.</p>
                </div>
            @endforelse
        </div>

        <!-- Bài viết -->
        <div id="posts" class="tab-content mt-4 hidden">
            @forelse ($posts as $post)
                <article class="post-article" data-id="{{ $post->id }}">
                    <div class="flex items-start gap-4">
                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            <a href="{{ route('users.show', $post->user->mention) }}">
                                <img src="{{ asset('storage/' . ($post->user->avatar ?? '')) }}"
                                    alt="{{ $post->user->name }}" class="h-20 w-20 rounded-full object-cover"
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
                                            <a
                                                href="{{ route('posts.edit', [$post->user->mention, $post->slug]) }}">Sửa</a>
                                            <form
                                                action="{{ route('posts.destroy', [$post->user->mention, $post->slug]) }}"
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
                                        $cleanQuery = ltrim($query, '#');
                                        $tags = array_map('trim', explode(',', $post->hashtag));
                                        if (in_array($cleanQuery, $tags, true)) {
                                            $tags = array_diff($tags, [$cleanQuery]);
                                            array_unshift($tags, $cleanQuery);
                                        }
                                    @endphp
                                    @foreach ($tags as $tag)
                                        <a href="{{ route('posts.byHashtag', ['hashtag' => $tag]) }}"
                                            class="text-xs px-2 py-1 rounded-full {{ strtolower($tag) === strtolower($cleanQuery) ? 'bg-black text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
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
                                                    <img data-src="{{ asset('storage/' . $m->media_path) }}"
                                                        alt="Ảnh bài viết" class="post-image" loading="lazy">
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

                            <!-- Actions (like/share/comment) -->
                            <div class="mt-4 flex items-center gap-4 text-sm text-gray-600">
                                <div class="flex items-center gap-2">
                                    @include('layouts.like', ['post' => $post])
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
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
            @empty
                <div class="bg-white rounded-2xl shadow-sm p-8 text-center">
                    <p class="text-gray-500 text-center">Không tìm thấy bài viết nào.</p>
                </div>

            @endforelse

            <!-- Pagination -->
            {{-- {{ $posts->links() }} --}}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Tab switching
            function showTab(tab) {
                document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
                document.getElementById(tab).classList.remove('hidden');

                document.querySelectorAll('.tab-link').forEach(el => el.classList.remove('active'));
                event.target.classList.add('active');
            }

            // Expose showTab to global scope for onclick
            window.showTab = showTab;

            // Lazy loading for posts and images
            const articles = document.querySelectorAll('.post-article');
            const images = document.querySelectorAll('.post-image');

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

            articles.forEach(article => articleObserver.observe(article));
            images.forEach(image => imageObserver.observe(image));

            // Like button with floating heart animation
            let isProcessing = false;

            document.addEventListener('submit', async function(event) {
                const form = event.target.closest('.like-form');
                if (!form || isProcessing) return;
                event.preventDefault();
                isProcessing = true;

                const button = form.querySelector('button');
                const likeIcon = button.querySelector('.like-icon');
                const likeCount = form.querySelector('.like-count');
                const url = form.getAttribute('action');
                const token = form.querySelector('input[name="_token"]').value;

                let count = parseInt(likeCount.textContent);
                const isLiked = likeIcon.classList.contains('fa-solid');

                if (isLiked) {
                    likeIcon.classList.remove('fa-solid', 'text-red-500');
                    likeIcon.classList.add('fa-regular', 'text-gray-500');
                    likeCount.textContent = count - 1;
                } else {
                    likeIcon.classList.remove('fa-regular', 'text-gray-500');
                    likeIcon.classList.add('fa-solid', 'text-red-500');
                    likeCount.textContent = count + 1;

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

                    likeCount.textContent = data.likes_count;
                } catch (error) {
                    console.error('Lỗi fetch:', error);
                    alert('Có lỗi xảy ra, vui lòng thử lại.');

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
                    isProcessing = false;
                }
            });

            // Dropdown toggle handler
            document.querySelectorAll('.dropdown-toggle').forEach(button => {
                button.addEventListener('click', () => {
                    const postId = button.getAttribute('data-post-id');
                    const dropdownMenu = document.getElementById(`dropdown-menu-${postId}`);
                    dropdownMenu.classList.toggle('show');

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
