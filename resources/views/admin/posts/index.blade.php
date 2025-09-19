@extends('admin.layouts.app')

@section('title', 'Manage Posts')
@section('page-title', 'Posts')

@section('content')
    <style>
        /* Existing CSS remains unchanged */
        .post-images {
            display: flex;
            gap: 8px;
            max-width: 100%;
            overflow-x: auto;
        }

        .post-image,
        .post-video {
            max-height: 100px;
            border-radius: 4px;
            object-fit: cover;
            cursor: pointer;
        }

        #media-popup {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 50;
            display: none;
        }

        #media-popup.show {
            display: flex;
        }

        #media-popup .popup-content {
            position: relative;
            background-color: white;
            border-radius: 8px;
            padding: 16px;
            max-width: 80vw;
            max-height: 80vh;
            overflow: auto;
        }

        #media-popup .popup-content img,
        #media-popup .popup-content video {
            max-width: 100%;
            max-height: 70vh;
            object-fit: contain;
            border-radius: 4px;
        }

        #media-popup .close-popup {
            position: absolute;
            top: 8px;
            right: 8px;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #4b5563;
            cursor: pointer;
        }

        #media-popup .close-popup:hover {
            color: #1f2937;
        }

        .toast {
            position: fixed;
            top: 16px;
            right: 16px;
            background-color: #dcfce7;
            color: #15803d;
            padding: 12px 24px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 100;
            opacity: 0;
            transform: translateX(100%);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .toast.show {
            opacity: 1;
            transform: translateX(0);
        }

        /* Filter form styles */
        .filter-form {
            display: none; /* Hidden by default */
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .filter-form.show {
            display: flex; /* Show when toggled */
        }

        .filter-form select,
        .filter-form input {
            padding: 8px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 14px;
        }

        .filter-form button {
            padding: 8px 16px;
            background-color: #3b82f6;
            color: white;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }

        .filter-form button:hover {
            background-color: #2563eb;
        }

        .filter-form .clear-btn {
            background-color: #ef4444;
        }

        .filter-form .clear-btn:hover {
            background-color: #dc2626;
        }

        /* Select2 custom styles */
        .select2-container {
            width: 200px !important;
        }

        .select2-container .select2-selection--single {
            height: 34px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
        }

        .select2-container .select2-selection__rendered {
            line-height: 34px;
            padding-left: 8px;
        }

        .select2-container .select2-selection__arrow {
            height: 34px;
        }

        /* Icon styles */
        .action-icon {
            font-size: 1.25rem;
            color: #4b5563;
            transition: color 0.2s, background-color 0.2s;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #d1d5db; /* Individual border for each icon */
            width: 36px; /* Fixed width for square shape */
            height: 36px; /* Fixed height for square shape */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .action-icon:hover {
            color: #1f2937;
            background-color: #e5e7eb;
        }

        .create-post-icon {
            color: #3b82f6;
        }

        .create-post-icon:hover {
            color: #2563eb;
        }

        .edit-icon {
            color: #eab308;
        }

        .edit-icon:hover {
            color: #ca8a04;
        }

        .delete-icon {
            color: #ef4444;
        }

        .delete-icon:hover {
            color: #dc2626;
        }

        .filter-toggle {
            cursor: pointer;
        }
    </style>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Posts</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.posts.create') }}" title="Create New Post">
                    <i class="fa-solid fa-plus action-icon create-post-icon"></i>
                </a>
                <i class="fa-solid fa-filter action-icon filter-toggle" title="Toggle Filters"></i>
            </div>
        </div>

        <!-- Filter Form -->
        <form action="{{ route('admin.posts.index') }}" method="GET" class="filter-form">
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700">User</label>
                <select name="user_id" id="user_id" class="mt-1 select2">
                    <option value="">All Users</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700">Date From</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="mt-1">
            </div>
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700">Date To</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="mt-1">
            </div>
            <div>
                <label for="hashtag" class="block text-sm font-medium text-gray-700">Hashtag</label>
                <input type="text" name="hashtag" id="hashtag" value="{{ request('hashtag') }}"
                    placeholder="Enter hashtag" class="mt-1">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit">Filter</button>
                <a href="{{ route('admin.posts.index') }}" class="clear-btn px-4 py-2 text-white rounded">Clear</a>
            </div>
        </form>
        <hr>
        @if (session('success'))
            <div id="toast" class="toast">
                {{ session('success') }}
            </div>
        @endif

        <div class="divide-y">
            @forelse ($posts as $post)
                <div class="flex items-center justify-between py-3">
                    <div class="flex items-center gap-3">
                        <div>
                            <div class="font-semibold">
                                {{ $post->title }}
                            </div>
                            <div class="text-sm text-gray-500">
                                Posted by: {{ $post->user->name ?? 'N/A' }} |
                                {{ $post->created_at->diffForHumans() }} | Hashtags: {{ $post->hashtag ?? 'None' }} |
                                Likes: {{ $post->likes_count }} | Shares: {{ $post->shares }} | Views:
                                {{ $post->views }}
                            </div>
                            @if ($post->room)
                                <div class="text-sm text-gray-500">
                                    Địa điểm: {{ $post->room->name ?? 'N/A' }}
                                </div>
                            @endif
                            @if ($post->media && $post->media->count())
                                <div class="post-images mt-2">
                                    @foreach ($post->media as $m)
                                        @if ($m->media_type === 'image')
                                            <img src="{{ asset('storage/' . $m->media_path) }}" alt="Post Image"
                                                class="post-image" loading="lazy">
                                        @elseif ($m->media_type === 'video')
                                            <video class="post-video">
                                                <source src="{{ asset('storage/' . $m->media_path) }}" type="video/mp4">
                                                Trình duyệt không hỗ trợ video.
                                            </video>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.posts.edit', $post->slug) }}" title="Edit Post">
                            <i class="fa-solid fa-pencil action-icon edit-icon"></i>
                        </a>
                        <form action="{{ route('admin.posts.destroy', $post->slug) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this post?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Delete Post" class="action-icon delete-icon">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 py-3">No posts found.</p>
            @endforelse
        </div>

        <!-- Popup Container -->
        <div id="media-popup">
            <div class="popup-content">
                <button class="close-popup">&times;</button>
                <div id="popup-media"></div>
            </div>
        </div>

        <div class="mt-4">
            {{ $posts->links() }}
        </div>
    </div>

    <!-- Include jQuery, Select2, and Font Awesome -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize Select2 for user dropdown
            $('#user_id').select2({
                placeholder: 'Select a user',
                allowClear: true,
                width: '100%'
            });

            // Handle Select2 value persistence after form submission
            const selectedUser = "{{ request('user_id') }}";
            if (selectedUser) {
                $('#user_id').val(selectedUser).trigger('change');
            }

            // Handle filter form toggle
            const filterToggle = document.querySelector('.filter-toggle');
            const filterForm = document.querySelector('.filter-form');
            filterToggle.addEventListener('click', () => {
                filterForm.classList.toggle('show');
            });

            // Handle media popup
            const mediaElements = document.querySelectorAll('.post-image, .post-video');
            const popup = document.getElementById('media-popup');
            const popupMedia = document.getElementById('popup-media');
            const closePopup = document.querySelector('.close-popup');

            mediaElements.forEach(media => {
                media.addEventListener('click', () => {
                    popupMedia.innerHTML = '';
                    if (media.tagName === 'IMG') {
                        const img = document.createElement('img');
                        img.src = media.src;
                        img.alt = media.alt;
                        popupMedia.appendChild(img);
                    } else if (media.tagName === 'VIDEO') {
                        const video = document.createElement('video');
                        video.src = media.querySelector('source').src;
                        video.controls = true;
                        video.autoplay = true;
                        popupMedia.appendChild(video);
                    }
                    popup.classList.add('show');
                });
            });

            closePopup.addEventListener('click', () => {
                popup.classList.remove('show');
            });

            popup.addEventListener('click', (e) => {
                if (e.target === popup) {
                    popup.classList.remove('show');
                }
            });

            // Handle toast notification
            const toast = document.getElementById('toast');
            if (toast) {
                setTimeout(() => {
                    toast.classList.add('show');
                }, 100);

                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => {
                        toast.remove();
                    }, 300);
                }, 3000);
            }
        });
    </script>
@endsection