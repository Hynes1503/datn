@extends('layouts.app')

@section('title', $post->title)

@section('content')
    <style>
        /* CSS for post creation form (not needed for show page, but keeping for consistency) */
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

        /* Additional CSS for like button */
        .like-button i.fa-heart {
            transition: color 0.2s ease;
        }

        .like-button:hover i.fa-heart {
            color: #e02424;
        }

        .like-button i.fa-solid {
            color: #e02424;
        }
    </style>

    <article class="post-article mb-6" data-id="{{ $post->id }}">
        <div class="flex items-start gap-4">
            {{-- Avatar --}}
            <div class="flex-shrink-0">
                <a href="{{ route('users.show', $post->user->mention) }}">
                    <img src="{{ asset('storage/' . ($post->user->avatar ?? '')) }}" alt="{{ $post->user->name }}"
                        class="h-24 w-24 rounded-full object-cover"
                        onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.png') }}';">
                </a>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between relative">
                    <div>
                        <h2 class="text-lg font-semibold">{{ $post->title }}</h2>
                        <div class="text-sm text-gray-500">Bởi <strong>{{ $post->user->name }}</strong> ·
                            {{ $post->created_at->diffForHumans() }}</div>
                    </div>

                    {{-- Options Button (visible only to post author) --}}
                    @if (auth()->check() && auth()->user()->id === $post->user->id)
                        <div class="dropdown">
                            <button class="dropdown-toggle" type="button" data-post-id="{{ $post->id }}">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <div class="dropdown-menu" id="dropdown-menu-{{ $post->id }}">
                                <a
                                    href="{{ route('posts.edit', ['user' => $post->user->mention, 'post' => $post->slug]) }}">Sửa</a>
                                <form
                                    action="{{ route('posts.destroy', ['user' => $post->user->mention, 'post' => $post->slug]) }}"
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

                {{-- Content --}}
                <p class="mt-3 text-sm text-gray-800">{!! nl2br(e($post->content)) !!}</p>

                {{-- Hashtags --}}
                @if ($post->hashtag)
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

                {{-- Actions (like/share/views) --}}
                <div class="mt-4 flex items-center gap-4 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        @auth
                            @include('layouts.like', ['post' => $post])
                        @else
                            <a href="{{ route('login') }}" class="flex items-center gap-2 hover:text-red-600">
                                <i class="fa-regular fa-heart"></i>
                                <span>{{ $post->likes()->count() }}</span>
                            </a>
                        @endauth
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
                {{-- Likes Details --}}
                @if ($post->likes()->count() > 0)
                    <div class="mt-4">
                        <h3 class="text-sm font-semibold text-gray-700">Lượt thích ({{ $post->likes()->count() }})</h3>
                        <p class="text-sm text-gray-600">Bài viết được thích bởi {{ $post->likes()->count() }} người.</p>
                    </div>
                @endif

                {{-- Comment Section --}}
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-700">Bình luận</h3>

                    {{-- Comment Form --}}
                    @auth
                        <form action="{{ route('comments.store', ['user' => $post->user->mention, 'post' => $post->slug]) }}"
                            method="POST" class="mt-4 comment-form">
                            @csrf
                            <div class="flex items-start gap-2">
                                <img src="{{ auth()->user()->getAvatarUrlAttribute() }}" alt="{{ auth()->user()->name }}"
                                    class="h-10 w-10 rounded-full object-cover">
                                <div class="flex-1">
                                    <textarea name="content" rows="3" class="w-full border rounded-lg p-2 text-sm" placeholder="Viết bình luận..."
                                        required></textarea>
                                    <button type="submit"
                                        class="mt-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Gửi</button>
                                </div>
                            </div>
                            <p class="text-red-500 text-sm mt-1 error-message hidden"></p>
                        </form>
                    @else
                        <p class="text-sm text-gray-600 mt-2">
                            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Đăng nhập</a> để bình luận.
                        </p>
                    @endauth

                    {{-- Comments List --}}
                    <div class="mt-6 space-y-4 comments-list">
                        @foreach ($post->comments as $comment)
                            <div class="flex items-start gap-3 comment border-t border-gray-200 pt-4"
                                data-comment-id="{{ $comment->id }}">
                                <img src="{{ $comment->user->getAvatarUrlAttribute() }}" alt="{{ $comment->user->name }}"
                                    class="h-10 w-10 rounded-full object-cover">
                                <div class="flex-1">
                                    <div class="bg-gray-100 rounded-lg p-3">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <a href="{{ route('users.show', $comment->user->mention) }}"
                                                    class="text-sm font-semibold text-gray-700">{{ $comment->user->name }}</a>
                                                <span
                                                    class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                            </div>
                                            @if (auth()->check() && (auth()->id() === $comment->user_id || auth()->user()->ownsPost($post)))
                                                <form
                                                    action="{{ route('comments.destroy', ['user' => $post->user->mention, 'post' => $post->slug, 'comment' => $comment->id]) }}"
                                                    method="POST" class="inline comment-delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-800 text-sm">Xóa</button>
                                                </form>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-800 mt-1">{!! nl2br(e($comment->content)) !!}</p>
                                    </div>

                                    {{-- Replies --}}
                                    @if ($comment->replies->count())
                                        <div class="ml-6 mt-2 space-y-4 replies-list">
                                            @foreach ($comment->replies as $reply)
                                                <div class="flex items-start gap-2 reply border-t border-gray-100 pt-2"
                                                    data-reply-id="{{ $reply->id }}">
                                                    <img src="{{ $reply->user->getAvatarUrlAttribute() }}"
                                                        alt="{{ $reply->user->name }}"
                                                        class="h-8 w-8 rounded-full object-cover">
                                                    <div class="flex-1">
                                                        <div class="bg-gray-50 rounded-lg p-2">
                                                            <div class="flex items-center justify-between">
                                                                <div>
                                                                    <a href="{{ route('users.show', $reply->user->mention) }}"
                                                                        class="text-xs font-semibold text-gray-700">{{ $reply->user->name }}</a>
                                                                    <span
                                                                        class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                                                                </div>
                                                                @if (auth()->check() && (auth()->id() === $reply->user_id || auth()->user()->ownsPost($post)))
                                                                    <form
                                                                        action="{{ route('comments.destroy', ['user' => $post->user->mention, 'post' => $post->slug, 'comment' => $reply->id]) }}"
                                                                        method="POST" class="inline comment-delete-form">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                            class="text-red-600 hover:text-red-800 text-xs">Xóa</button>
                                                                    </form>
                                                                @endif
                                                            </div>
                                                            <p class="text-xs text-gray-800 mt-1">{!! nl2br(e($reply->content)) !!}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Reply Form --}}
                                    @auth
                                        <form
                                            action="{{ route('comments.store', ['user' => $post->user->mention, 'post' => $post->slug]) }}"
                                            method="POST" class="mt-2 ml-6 reply-form">
                                            @csrf
                                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                            <div class="flex items-start gap-2">
                                                <img src="{{ auth()->user()->getAvatarUrlAttribute() }}"
                                                    alt="{{ auth()->user()->name }}"
                                                    class="h-8 w-8 rounded-full object-cover">
                                                <div class="flex-1">
                                                    <textarea name="content" rows="2" class="w-full border rounded-lg p-2 text-xs"
                                                        placeholder="Trả lời {{ $comment->user->name }}..." required></textarea>
                                                    <button type="submit"
                                                        class="mt-1 bg-blue-600 text-white px-3 py-1 rounded-lg hover:bg-blue-700 text-xs">Trả
                                                        lời</button>
                                                </div>
                                            </div>
                                            <p class="text-red-500 text-xs mt-1 error-message hidden"></p>
                                        </form>
                                    @endauth
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </article>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const article = document.querySelector('.post-article');
            const images = document.querySelectorAll('.post-image');

            // Observer for article
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

            // Observe article and images
            if (article) articleObserver.observe(article);
            images.forEach(image => imageObserver.observe(image));

            // Like button handler
            document.addEventListener("submit", async (e) => {
                if (e.target.classList.contains("like-form")) {
                    e.preventDefault();
                    const form = e.target;
                    const button = form.querySelector('.like-button');
                    const icon = button.querySelector('i');
                    const likeCountSpan = button.querySelector('.like-count');
                    const currentLikes = parseInt(likeCountSpan.textContent);
                    const isLiked = icon.classList.contains('fa-solid');

                    // Optimistic UI update
                    if (isLiked) {
                        icon.classList.remove('fa-solid', 'text-red-600');
                        icon.classList.add('fa-regular');
                        likeCountSpan.textContent = currentLikes - 1;
                    } else {
                        icon.classList.remove('fa-regular');
                        icon.classList.add('fa-solid', 'text-red-600');
                        likeCountSpan.textContent = currentLikes + 1;
                    }

                    try {
                        const response = await fetch(form.action, {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": form.querySelector('input[name="_token"]')
                                    .value,
                                "X-Requested-With": "XMLHttpRequest",
                                "Accept": "application/json",
                            },
                        });

                        const data = await response.json();
                        if (data.success) {
                            likeCountSpan.textContent = data
                            .likes; // Update with server-confirmed count
                            icon.classList.toggle('fa-solid', data.is_liked);
                            icon.classList.toggle('fa-regular', !data.is_liked);
                            icon.classList.toggle('text-red-600', data.is_liked);
                        } else {
                            // Revert UI on failure
                            icon.classList.toggle('fa-solid', !isLiked);
                            icon.classList.toggle('fa-regular', isLiked);
                            icon.classList.toggle('text-red-600', isLiked);
                            likeCountSpan.textContent = currentLikes;
                            alert(data.error || 'Có lỗi xảy ra khi thích bài viết.');
                        }
                    } catch (err) {
                        // Revert UI on error
                        icon.classList.toggle('fa-solid', !isLiked);
                        icon.classList.toggle('fa-regular', isLiked);
                        icon.classList.toggle('text-red-600', isLiked);
                        likeCountSpan.textContent = currentLikes;
                        console.error('Error toggling like:', err);
                        alert('Có lỗi xảy ra khi thích bài viết.');
                    }
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

            // Handle comment/reply form submission via AJAX
            document.querySelectorAll('.comment-form, .reply-form').forEach(form => {
                form.addEventListener('submit', async function(event) {
                    event.preventDefault();
                    const formData = new FormData(this);
                    const errorMessage = this.querySelector('.error-message');
                    errorMessage.classList.add('hidden');

                    try {
                        const response = await fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': formData.get('_token')
                            }
                        });

                        const data = await response.json();
                        if (data.success) {
                            const comment = data.comment;
                            const commentsList = document.querySelector('.comments-list');
                            const repliesList = this.closest('.comment')?.querySelector(
                                '.replies-list');

                            const isReply = !!repliesList;
                            const newCommentHtml = `
                                <div class="flex items-start gap-3 ${isReply ? 'reply border-t border-gray-100 pt-2' : 'comment border-t border-gray-200 pt-4'}" data-${isReply ? 'reply' : 'comment'}-id="${comment.id}">
                                    <img src="${comment.user.avatar_url || '{{ asset('images/default-avatar.png') }}'}"
                                        alt="${comment.user.name}"
                                        class="h-${isReply ? '8' : '10'} w-${isReply ? '8' : '10'} rounded-full object-cover"
                                        onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.png') }}';">
                                    <div class="flex-1">
                                        <div class="bg-${isReply ? 'gray-50' : 'gray-100'} rounded-lg p-${isReply ? '2' : '3'}">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <a href="/@${comment.user.mention}"
                                                        class="text-${isReply ? 'xs' : 'sm'} font-semibold text-gray-700">${comment.user.name}</a>
                                                    <span class="text-xs text-gray-500">vừa xong</span>
                                                </div>
                                                ${comment.can_delete ? `
                                                        <form action="/@${comment.user.mention}/posts/${comment.post_id}/comments/${comment.id}"
                                                            method="POST" class="inline comment-delete-form">
                                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                            <input type="hidden" name="_method" value="DELETE">
                                                            <button type="submit" class="text-red-600 hover:text-red-800 text-${isReply ? 'xs' : 'sm'}">Xóa</button>
                                                        </form>
                                                    ` : ''}
                                            </div>
                                            <p class="text-${isReply ? 'xs' : 'sm'} text-gray-800 mt-1">${comment.content.replace(/\n/g, '<br>')}</p>
                                        </div>
                                    </div>
                                </div>
                            `;

                            if (isReply) {
                                if (repliesList) {
                                    repliesList.insertAdjacentHTML('beforeend', newCommentHtml);
                                } else {
                                    console.error('Replies list not found for comment ID:',
                                        comment.id);
                                }
                            } else {
                                if (commentsList) {
                                    commentsList.insertAdjacentHTML('afterbegin',
                                        newCommentHtml);
                                } else {
                                    console.error('Comments list not found');
                                }
                            }

                            this.querySelector('textarea').value = '';
                        } else {
                            errorMessage.textContent = data.error ||
                                'Có lỗi xảy ra khi gửi bình luận.';
                            errorMessage.classList.remove('hidden');
                        }
                    } catch (error) {
                        console.error('Error submitting comment:', error);
                        errorMessage.textContent = 'Có lỗi xảy ra khi gửi bình luận.';
                        errorMessage.classList.remove('hidden');
                    }
                });
            });

            // Handle comment/reply deletion via AJAX
            document.querySelector('.comments-list').addEventListener('submit', async function(event) {
                if (event.target.matches('.comment-delete-form')) {
                    event.preventDefault();
                    const form = event.target;
                    const formData = new FormData(form);

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': formData.get('_token')
                            }
                        });

                        const data = await response.json();
                        if (data.success) {
                            const commentElement = form.closest('.comment, .reply');
                            commentElement.remove();
                        } else {
                            alert(data.error || 'Có lỗi xảy ra khi xóa bình luận.');
                        }
                    } catch (error) {
                        alert('Có lỗi xảy ra khi xóa bình luận.');
                        console.error('Error deleting comment:', error);
                    }
                }
            });
        });
    </script>
@endsection
