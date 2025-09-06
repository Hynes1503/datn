@extends('layouts.app')

@section('title', $post->title)

@section('content')
    <article class="mb-6 bg-white rounded-2xl shadow-sm p-4">
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
                        <div>
                            <button class="text-gray-600 hover:text-gray-900 focus:outline-none"
                                onclick="toggleOptionsMenu('options-{{ $post->id }}')">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v.01M12 12v.01M12 18v.01" />
                                </svg>
                            </button>
                            <div id="options-{{ $post->id }}"
                                class="hidden absolute right-0 mt-2 w-32 bg-white rounded-md shadow-lg z-10">
                                <a href="{{ route('posts.edit', ['user' => $user->mention, 'post' => $post->slug]) }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sửa</a>
                                <form
                                    action="{{ route('posts.destroy', ['user' => $user->mention, 'post' => $post->slug]) }}"
                                    method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Xóa</button>
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
                                        <img src="{{ asset('storage/' . $m->media_path) }}" alt="Ảnh bài viết"
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

                {{-- Actions (like only) --}}
                <div class="mt-4 flex items-center gap-4 text-sm text-gray-600">
                    <form action="#" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 hover:text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                            </svg>
                            <span>{{ $post->likes ?? 0 }}</span>
                        </button>
                    </form>

                    <div class="ml-auto text-gray-400">{{ $post->views ?? 0 }} lượt xem</div>
                </div>

                {{-- Likes Details --}}
                @if ($post->likes > 0)
                    <div class="mt-4">
                        <h3 class="text-sm font-semibold text-gray-700">Lượt thích ({{ $post->likes }})</h3>
                        <p class="text-sm text-gray-600">Bài viết được thích bởi {{ $post->likes }} người.</p>
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
                                                    action="{{ route('comments.destroy', ['user' => $user->mention, 'post' => $post->slug, 'comment' => $comment->id]) }}"
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
                                                                        action="{{ route('comments.destroy', ['user' => $user->mention, 'post' => $post->slug, 'comment' => $reply->id]) }}"
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
                                            action="{{ route('comments.store', ['user' => $user->mention, 'post' => $post->slug]) }}"
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

        // Handle comment/reply form submission via AJAX
        document.querySelectorAll('.comment-form, .reply-form').forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                const formData = new FormData(this);
                const errorMessage = this.querySelector('.error-message');
                errorMessage.classList.add('hidden');

                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': formData.get('_token')
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            const comment = data.comment;
                            const commentsList = document.querySelector('.comments-list');
                            const repliesList = this.closest('.comment')?.querySelector(
                            '.replies-list');

                            // Log to verify data
                            console.log('New comment:', comment);

                            // Create new comment/reply HTML
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

                            // Append to appropriate list
                            if (isReply) {
                                if (repliesList) {
                                    repliesList.insertAdjacentHTML('beforeend', newCommentHtml);
                                } else {
                                    console.error('Replies list not found for comment ID:', comment.id);
                                }
                            } else {
                                if (commentsList) {
                                    commentsList.insertAdjacentHTML('afterbegin', newCommentHtml);
                                } else {
                                    console.error('Comments list not found');
                                }
                            }

                            // Clear form
                            this.querySelector('textarea').value = '';
                        } else {
                            errorMessage.textContent = data.error || 'Có lỗi xảy ra khi gửi bình luận.';
                            errorMessage.classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error submitting comment:', error);
                        errorMessage.textContent = 'Có lỗi xảy ra khi gửi bình luận.';
                        errorMessage.classList.remove('hidden');
                    });
            });
        });
        // Handle comment/reply deletion via AJAX using event delegation
        document.querySelector('.comments-list').addEventListener('submit', function(event) {
            if (event.target.matches('.comment-delete-form')) {
                event.preventDefault();
                const form = event.target;
                const formData = new FormData(form);

                fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': formData.get('_token')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const commentElement = form.closest('.comment, .reply');
                            commentElement.remove();
                        } else {
                            alert(data.error || 'Có lỗi xảy ra khi xóa bình luận.');
                        }
                    })
                    .catch(() => {
                        alert('Có lỗi xảy ra khi xóa bình luận.');
                    });
            }
        });
    </script>
@endsection
