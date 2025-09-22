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

        /* Additional CSS for comment icon */
        .comment-icon {
            transition: color 0.2s ease;
        }

        .comment-icon:hover {
            color: #2563eb; /* Blue color to match common UI themes */
        }

        /* Ensure active hashtag doesn't change on hover */
        .bg-black:hover {
            background-color: #000 !important;
        }

        /* Ensure visibility toggle works */
        .hidden {
            display: none;
        }
    </style>
    @include('layouts._reportform')
    <article class="post-article mb-6" data-id="{{ $post->id }}">
        <div class="flex items-start gap-4">
            {{-- Avatar --}}
            <div class="flex-shrink-0 text-center w-20">
                <a href="{{ route('users.show', $post->user->mention) }}">
                    <img src="{{ asset('storage/' . ($post->user->avatar ?? '')) }}" alt="{{ $post->user->name }}"
                        class="h-20 w-20 rounded-full object-cover mx-auto"
                        onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.png') }}';">
                </a>
                <a href="{{ route('users.show', $post->user->mention) }}" class="hover:underline">
                    <strong class="block text-sm break-words leading-tight">
                        {{ $post->user->name }}
                    </strong>
                </a>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between relative">
                    <div>
                        <h2 class="text-lg font-semibold">{{ $post->title }}</h2>
                        <div class="text-sm text-gray-500">
                            {{ $post->created_at->format('d/m/Y') }} ·
                            {{ $post->created_at->diffForHumans() }}
                        </div>
                    </div>

                    {{-- Options Button (visible only to post author) --}}
                    @if (auth()->check() && auth()->user()->id === $post->user->id)
                        {{-- Dropdown cho chủ sở hữu (Edit + Delete) --}}
                        <div class="dropdown relative">
                            <button class="dropdown-toggle" type="button" data-post-id="{{ $post->id }}">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <div class="dropdown-menu absolute right-0 mt-2 w-32 bg-white border rounded shadow-lg hidden z-50"
                                id="dropdown-menu-{{ $post->id }}">
                                <a href="{{ route('posts.edit', ['user' => $post->user->mention, 'post' => $post->slug]) }}"
                                    class="block px-4 py-2 text-sm hover:bg-gray-100">Sửa</a>

                                <form
                                    action="{{ route('posts.destroy', ['user' => $post->user->mention, 'post' => $post->slug]) }}"
                                    method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full text-left block px-4 py-2 text-sm text-red-500 hover:bg-gray-100"
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này?');">
                                        Xóa
                                    </button>
                                </form>
                            </div>
                        </div>
                    @elseif(auth()->check())
                        {{-- Dropdown cho người khác (Report) --}}
                        <div class="dropdown relative">
                            <button class="dropdown-toggle" type="button" data-post-id="{{ $post->id }}">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <div class="dropdown-menu absolute right-0 mt-2 w-32 bg-white border rounded shadow-lg hidden z-50"
                                id="dropdown-menu-{{ $post->id }}">
                                <button type="button" onclick="openReportModal('post', {{ $post->id }})"
                                    class="w-full text-left block px-4 py-2 text-sm text-black hover:bg-gray-100">
                                    Báo cáo
                                </button>
                            </div>
                        </div>
                    @endif

                </div>
                @if ($post->room)
                    <div class="flex items-center gap-1 text-gray-600 mt-1">
                        <i class="fa-solid fa-location-dot text-red-500"></i>
                        <span>{{ $post->room->name }}</span>
                    </div>
                @endif
                <hr>
                {{-- Content --}}
                <p class="mt-3 text-sm text-gray-800">{!! nl2br(e($post->content)) !!}</p>

                {{-- Hashtags --}}
                @if ($post->hashtag)
                    <div class="mt-3 flex flex-wrap gap-2">
                        @php
                            // Lấy danh sách hashtag từ bài viết, loại bỏ ký tự # và chuẩn hóa
                            $tags = array_map('trim', explode(',', str_replace('#', '', $post->hashtag)));
                            // Lấy danh sách hashtag hiện tại từ URL (nếu có)
                            $currentHashtags = request()->route('hashtag')
                                ? array_map('trim', explode(',', request()->route('hashtag')))
                                : [];
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
                                    $newHashtagList = $currentHashtags
                                        ? implode(',', $currentHashtags) . ',' . $tag
                                        : $tag;
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
                        <i class="fa-regular fa-comment comment-icon"></i>
                        <span>{{ $post->allcomments()->count() ?? 0 }}</span>
                    </div>
                    <div class="ml-auto text-gray-400">{{ $post->views ?? 0 }} lượt xem</div>
                </div>

                <hr class="mt-5 border-t-2 border-gray-450">
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
                                    <button
                                        class="mt-2 px-3 py-1 text-sm border border-black rounded-lg bg-transparent text-black hover:bg-black hover:text-white transition">
                                        Gửi
                                    </button>
                                </div>
                            </div>
                            <p class="text-red-500 text-sm mt-1 error-message hidden"></p>
                        </form>
                    @else
                        <p class="text-sm text-gray-600 mt-2">
                            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Đăng nhập</a> để bình
                            luận.
                        </p>
                    @endauth

                    {{-- Comments List --}}
                    <div class="mt-6 space-y-4 comments-list">
                        @foreach ($comments as $comment)
                            <div class="flex items-start gap-3 comment border-t border-gray-200 pt-4"
                                data-comment-id="{{ $comment->id }}">
                                <img src="{{ $comment->user->getAvatarUrlAttribute() }}" alt="{{ $comment->user->name }}"
                                    class="h-10 w-10 rounded-full object-cover">
                                <div class="flex-1">
                                    <div class="bg-gray-100 rounded-lg p-3 comment-content">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <a href="{{ route('users.show', $comment->user->mention) }}"
                                                    class="text-sm font-semibold text-gray-700">{{ $comment->user->name }}</a>
                                                <span
                                                    class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                            </div>

                                            {{-- Dropdown --}}
                                            @if (auth()->check())
                                                <div class="dropdown relative">
                                                    <button class="dropdown-toggle" type="button"
                                                        data-comment-id="{{ $comment->id }}">
                                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu absolute right-0 mt-2 w-36 bg-white border rounded shadow-lg hidden z-50"
                                                        id="dropdown-menu-comment-{{ $comment->id }}">

                                                        {{-- Nếu là chủ sở hữu comment --}}
                                                        @if (auth()->id() === $comment->user_id)
                                                            <button type="button"
                                                                class="edit-comment-btn block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                                                                data-comment-id="{{ $comment->id }}">
                                                                Sửa
                                                            </button>
                                                            <form
                                                                action="{{ route('comments.destroy', ['user' => $post->user->mention, 'post' => $post->slug, 'comment' => $comment->id]) }}"
                                                                method="POST" class="inline comment-delete-form w-full">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="block w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-gray-100"
                                                                    onclick="return confirm('Bạn có chắc chắn muốn xóa bình luận này?');">
                                                                    Xóa
                                                                </button>
                                                            </form>

                                                            {{-- Nếu là chủ sở hữu bài viết (và KHÔNG phải chủ sở hữu comment) --}}
                                                        @elseif(auth()->id() === $post->user_id)
                                                            <form
                                                                action="{{ route('comments.destroy', ['user' => $post->user->mention, 'post' => $post->slug, 'comment' => $comment->id]) }}"
                                                                method="POST" class="inline comment-delete-form w-full">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="block w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-gray-100"
                                                                    onclick="return confirm('Bạn có chắc chắn muốn xóa bình luận này?');">
                                                                    Xóa
                                                                </button>
                                                            </form>
                                                            <button type="button"
                                                                onclick="openReportModal('comment', {{ $comment->id }})"
                                                                class="block w-full text-left px-4 py-2 text-sm text-black hover:bg-gray-100">
                                                                Báo cáo
                                                            </button>

                                                            {{-- Người khác --}}
                                                        @else
                                                            <button type="button"
                                                                onclick="openReportModal('comment', {{ $comment->id }})"
                                                                class="block w-full text-left px-4 py-2 text-sm text-black hover:bg-gray-100">
                                                                Báo cáo
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <p class="text-sm text-gray-800 mt-1 comment-text">{!! nl2br(e($comment->content)) !!}</p>
                                    </div>
                                    <!-- Edit Comment Form (hidden by default) -->
                                    <form
                                        action="{{ route('comments.update', ['user' => $post->user->mention, 'post' => $post->slug, 'comment' => $comment->id]) }}"
                                        method="POST" class="mt-2 edit-comment-form hidden"
                                        data-comment-id="{{ $comment->id }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="flex items-start gap-2">
                                            <img src="{{ auth()->user()->getAvatarUrlAttribute() }}"
                                                alt="{{ auth()->user()->name }}"
                                                class="h-10 w-10 rounded-full object-cover">
                                            <div class="flex-1">
                                                <textarea name="content" rows="3" class="w-full border rounded-lg p-2 text-sm" required>{{ $comment->content }}</textarea>
                                                <div class="flex gap-2">
                                                    <button type="submit"
                                                        class="mt-2 px-3 py-1 text-sm border border-black rounded-lg bg-transparent text-black hover:bg-black hover:text-white transition">
                                                        Cập nhật
                                                    </button>
                                                    <button type="button"
                                                        class="mt-2 px-3 py-1 text-sm border border-gray-300 rounded-lg bg-transparent text-gray-600 hover:bg-gray-200 cancel-edit-btn">
                                                        Hủy
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-red-500 text-sm mt-1 error-message hidden"></p>
                                    </form>

                                    {{-- Replies --}}
                                    @if ($comment->replies->count())
                                        <div class="ml-6 mt-2 space-y-4 replies-list">
                                            @foreach ($comment->replies as $reply)
                                                <div class="flex items-start gap-2 reply pt-2"
                                                    data-comment-id="{{ $reply->id }}">
                                                    <img src="{{ $reply->user->getAvatarUrlAttribute() }}"
                                                        alt="{{ $reply->user->name }}"
                                                        class="h-8 w-8 rounded-full object-cover">
                                                    <div class="flex-1">
                                                        <div class="bg-gray-100 rounded-lg p-2 comment-content">
                                                            <div class="flex items-center justify-between">
                                                                <div>
                                                                    <a href="{{ route('users.show', $reply->user->mention) }}"
                                                                        class="text-xs font-semibold text-gray-700">{{ $reply->user->name }}</a>
                                                                    <span
                                                                        class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                                                                </div>

                                                                {{-- Dropdown cho reply --}}
                                                                @if (auth()->check())
                                                                    <div class="dropdown relative">
                                                                        <button class="dropdown-toggle" type="button"
                                                                            data-comment-id="{{ $reply->id }}">
                                                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                                                        </button>
                                                                        <div class="dropdown-menu absolute right-0 mt-2 w-36 bg-white border rounded shadow-lg hidden z-50"
                                                                            id="dropdown-menu-comment-{{ $reply->id }}">

                                                                            {{-- Nếu là chủ sở hữu reply --}}
                                                                            @if (auth()->id() === $reply->user_id)
                                                                                <button type="button"
                                                                                    class="edit-comment-btn block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                                                                                    data-comment-id="{{ $reply->id }}">
                                                                                    Sửa
                                                                                </button>
                                                                                <form
                                                                                    action="{{ route('comments.destroy', ['user' => $post->user->mention, 'post' => $post->slug, 'comment' => $reply->id]) }}"
                                                                                    method="POST"
                                                                                    class="inline comment-delete-form w-full">
                                                                                    @csrf
                                                                                    @method('DELETE')
                                                                                    <button type="submit"
                                                                                        class="block w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-gray-100"
                                                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa phản hồi này?');">
                                                                                        Xóa
                                                                                    </button>
                                                                                </form>

                                                                                {{-- Nếu là chủ sở hữu bài viết (và KHÔNG phải chủ sở hữu reply) --}}
                                                                            @elseif(auth()->id() === $post->user_id)
                                                                                <form
                                                                                    action="{{ route('comments.destroy', ['user' => $post->user->mention, 'post' => $post->slug, 'comment' => $reply->id]) }}"
                                                                                    method="POST"
                                                                                    class="inline comment-delete-form w-full">
                                                                                    @csrf
                                                                                    @method('DELETE')
                                                                                    <button type="submit"
                                                                                        class="block w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-gray-100"
                                                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa phản hồi này?');">
                                                                                        Xóa
                                                                                    </button>
                                                                                </form>
                                                                                <button type="button"
                                                                                    onclick="openReportModal('comment', {{ $reply->id }})"
                                                                                    class="block w-full text-left px-4 py-2 text-sm text-black hover:bg-gray-100">
                                                                                    Báo cáo
                                                                                </button>

                                                                                {{-- Người khác --}}
                                                                            @else
                                                                                <button type="button"
                                                                                    onclick="openReportModal('comment', {{ $reply->id }})"
                                                                                    class="block w-full text-left px-4 py-2 text-sm text-black hover:bg-gray-100">
                                                                                    Báo cáo
                                                                                </button>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <p class="text-xs text-gray-800 mt-1 comment-text">
                                                                {!! nl2br(e($reply->content)) !!}</p>
                                                        </div>
                                                        <!-- Edit Reply Form (hidden by default) -->
                                                        <form
                                                            action="{{ route('comments.update', ['user' => $post->user->mention, 'post' => $post->slug, 'comment' => $reply->id]) }}"
                                                            method="POST" class="mt-2 edit-comment-form hidden"
                                                            data-comment-id="{{ $reply->id }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="flex items-start gap-2">
                                                                <img src="{{ auth()->user()->getAvatarUrlAttribute() }}"
                                                                    alt="{{ auth()->user()->name }}"
                                                                    class="h-8 w-8 rounded-full object-cover">
                                                                <div class="flex-1">
                                                                    <textarea name="content" rows="2" class="w-full border rounded-lg p-2 text-xs" required>{{ $reply->content }}</textarea>
                                                                    <div class="flex gap-2">
                                                                        <button type="submit"
                                                                            class="mt-1 px-3 py-1 text-xs border border-black rounded-lg bg-transparent text-black hover:bg-black hover:text-white transition">
                                                                            Cập nhật
                                                                        </button>
                                                                        <button type="button"
                                                                            class="mt-1 px-3 py-1 text-xs border border-gray-300 rounded-lg bg-transparent text-gray-600 hover:bg-gray-200 cancel-edit-btn">
                                                                            Hủy
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <p class="text-red-500 text-xs mt-1 error-message hidden">
                                                            </p>
                                                        </form>
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
                                                        placeholder="Phản hồi {{ $comment->user->name }}..." required></textarea>
                                                    <button type="submit"
                                                        class="mt-1 px-3 py-1 text-xs border border-black text-black rounded-lg bg-transparent hover:bg-black hover:text-white transition">
                                                        Phản hồi
                                                    </button>
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
                                .likes_count; // Update with server-confirmed count
                            icon.classList.toggle('fa-solid', data.liked);
                            icon.classList.toggle('fa-regular', !data.liked);
                            icon.classList.toggle('text-red-600', data.liked);
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
                    const commentId = button.getAttribute('data-comment-id');
                    const dropdownMenu = postId ?
                        document.getElementById(`dropdown-menu-${postId}`) :
                        document.getElementById(`dropdown-menu-comment-${commentId}`);
                    dropdownMenu.classList.toggle('show');

                    // Close other open dropdowns
                    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                        if (menu.id !== `dropdown-menu-${postId}` && menu.id !==
                            `dropdown-menu-comment-${commentId}`) {
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
                                <div class="flex items-start gap-3 ${isReply ? 'reply border-t border-gray-100 pt-2' : 'comment border-t border-gray-200 pt-4'}" data-comment-id="${comment.id}">
                                    <img src="${comment.user.avatar_url || '{{ asset('images/default-avatar.png') }}'}"
                                         alt="${comment.user.name}"
                                         class="h-${isReply ? '8' : '10'} w-${isReply ? '8' : '10'} rounded-full object-cover"
                                         onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.png') }}';">
                                    <div class="flex-1">
                                        <div class="bg-${isReply ? 'gray-50' : 'gray-100'} rounded-lg p-${isReply ? '2' : '3'} comment-content">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <a href="/@${comment.user.mention}"
                                                       class="text-${isReply ? 'xs' : 'sm'} font-semibold text-gray-700">${comment.user.name}</a>
                                                    <span class="text-xs text-gray-500">vừa xong</span>
                                                </div>
                                                ${comment.can_delete ? `
                                                                                                        <div class="dropdown">
                                                                                                            <button class="dropdown-toggle" type="button" data-comment-id="${comment.id}">
                                                                                                                <i class="fa-solid fa-ellipsis-vertical"></i>
                                                                                                            </button>
                                                                                                            <div class="dropdown-menu" id="dropdown-menu-comment-${comment.id}">
                                                                                                                <button type="button" class="edit-comment-btn block w-full text-left px-4 py-2 text-sm hover:bg-gray-100" data-comment-id="${comment.id}">Sửa</button>
                                                                                                                <form action="/@${comment.user.mention}/posts/${comment.post_id}/comments/${comment.id}"
                                                                                                                      method="POST" class="inline comment-delete-form w-full">
                                                                                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                                                                                    <input type="hidden" name="_method" value="DELETE">
                                                                                                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-gray-100"
                                                                                                                            onclick="return confirm('Bạn có chắc chắn muốn xóa ${isReply ? 'phản hồi' : 'bình luận'} này?');">Xóa</button>
                                                                                                                </form>
                                                                                                                ${comment.can_report && !comment.can_delete ? `
                                                                                                            <button type="button" onclick="openReportModal('comment', ${comment.id})"
                                                                                                                    class="block w-full text-left px-4 py-2 text-sm text-black hover:bg-gray-100">Báo cáo</button>
                                                                                                        ` : ''}
                                                                                                                ${comment.can_delete && comment.post_owner && !comment.is_owner ? `
                                                                                                            <button type="button" onclick="openReportModal('comment', ${comment.id})"
                                                                                                                    class="block w-full text-left px-4 py-2 text-sm text-black hover:bg-gray-100">Báo cáo</button>
                                                                                                        ` : ''}
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    ` : comment.can_report ? `
                                                                                                        <div class="dropdown">
                                                                                                            <button class="dropdown-toggle" type="button" data-comment-id="${comment.id}">
                                                                                                                <i class="fa-solid fa-ellipsis-vertical"></i>
                                                                                                            </button>
                                                                                                            <div class="dropdown-menu" id="dropdown-menu-comment-${comment.id}">
                                                                                                                <button type="button" onclick="openReportModal('comment', ${comment.id})"
                                                                                                                        class="block w-full text-left px-4 py-2 text-sm text-black hover:bg-gray-100">Báo cáo</button>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    ` : ''}
                                            </div>
                                            <p class="text-${isReply ? 'xs' : 'sm'} text-gray-800 mt-1 comment-text">${comment.content.replace(/\n/g, '<br>')}</p>
                                        </div>
                                        <form action="/@${comment.user.mention}/posts/${comment.post_id}/comments/${comment.id}"
                                              method="POST" class="mt-2 edit-comment-form hidden" data-comment-id="${comment.id}">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="hidden" name="_method" value="PUT">
                                            <div class="flex items-start gap-2">
                                                <img src="${comment.user.avatar_url || '{{ asset('images/default-avatar.png') }}'}"
                                                     alt="${comment.user.name}"
                                                     class="h-${isReply ? '8' : '10'} w-${isReply ? '8' : '10'} rounded-full object-cover">
                                                <div class="flex-1">
                                                    <textarea name="content" rows="${isReply ? '2' : '3'}" class="w-full border rounded-lg p-2 text-${isReply ? 'xs' : 'sm'}"
                                                              required>${comment.content}</textarea>
                                                    <div class="flex gap-2">
                                                        <button type="submit"
                                                                class="mt-${isReply ? '1' : '2'} px-3 py-1 text-${isReply ? 'xs' : 'sm'} border border-black rounded-lg bg-transparent text-black hover:bg-black hover:text-white transition">
                                                            Cập nhật
                                                        </button>
                                                        <button type="button"
                                                                class="mt-${isReply ? '1' : '2'} px-3 py-1 text-${isReply ? 'xs' : 'sm'} border border-gray-300 rounded-lg bg-transparent text-gray-600 hover:bg-gray-200 cancel-edit-btn">
                                                            Hủy
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="text-red-500 text-${isReply ? 'xs' : 'sm'} mt-1 error-message hidden"></p>
                                        </form>
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

            // Handle edit comment button
            document.querySelectorAll('.edit-comment-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const commentId = button.getAttribute('data-comment-id');
                    const commentElement = document.querySelector(
                        `[data-comment-id="${commentId}"]`);
                    const commentContent = commentElement.querySelector('.comment-content');
                    const editForm = commentElement.querySelector('.edit-comment-form');
                    const dropdownMenu = document.getElementById(
                        `dropdown-menu-comment-${commentId}`);

                    if (commentContent && editForm && dropdownMenu) {
                        // Hide dropdown
                        dropdownMenu.classList.remove('show');
                        // Hide comment content and show edit form
                        commentContent.classList.add('hidden');
                        editForm.classList.remove('hidden');
                    } else {
                        console.error(
                            'Could not find comment content, edit form, or dropdown for comment ID:',
                            commentId);
                    }
                });
            });

            // Handle cancel edit button
            document.querySelectorAll('.cancel-edit-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const editForm = button.closest('.edit-comment-form');
                    const commentElement = editForm.closest('.comment, .reply');
                    const commentContent = commentElement.querySelector('.comment-content');

                    if (commentContent && editForm) {
                        // Hide edit form and show comment content
                        editForm.classList.add('hidden');
                        commentContent.classList.remove('hidden');
                    } else {
                        console.error(
                            'Could not find comment content or edit form for cancel action');
                    }
                });
            });

            // Handle edit comment form submission via AJAX
            document.querySelectorAll('.edit-comment-form').forEach(form => {
                form.addEventListener('submit', async function(event) {
                    event.preventDefault();
                    const formData = new FormData(this);
                    const commentId = this.getAttribute('data-comment-id');
                    const commentElement = document.querySelector(
                        `[data-comment-id="${commentId}"]`);
                    const commentContent = commentElement.querySelector('.comment-content');
                    const commentText = commentElement.querySelector('.comment-text');
                    const errorMessage = this.querySelector('.error-message');

                    if (!commentContent || !commentText || !errorMessage) {
                        console.error(
                            'Could not find comment content, text, or error message for comment ID:',
                            commentId);
                        return;
                    }

                    errorMessage.classList.add('hidden');

                    try {
                        const response = await fetch(this.action, {
                            method: 'POST', // Laravel handles PUT via _method
                            body: formData,
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': formData.get('_token')
                            }
                        });

                        const data = await response.json();
                        if (data.success) {
                            // Update the comment text
                            commentText.innerHTML = data.comment.content.replace(/\n/g, '<br>');
                            // Hide edit form and show comment content
                            this.classList.add('hidden');
                            commentContent.classList.remove('hidden');
                        } else {
                            errorMessage.textContent = data.error ||
                                'Có lỗi xảy ra khi sửa bình luận.';
                            errorMessage.classList.remove('hidden');
                        }
                    } catch (error) {
                        console.error('Error updating comment:', error);
                        errorMessage.textContent = 'Có lỗi xảy ra khi sửa bình luận.';
                        errorMessage.classList.remove('hidden');
                    }
                });
            });
        });
    </script>
@endsection