@extends('admin.layouts.app')

@section('title', 'Quản lý người dùng')
@section('page-title', 'Chi tiết người dùng')

@section('content')
    <style>
        /* Post media styles */
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

        /* Toast notification */
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

        .toast.error {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        /* Icon styles */
        .action-icon {
            font-size: 1.25rem;
            color: #4b5563;
            transition: color 0.2s, background-color 0.2s;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #d1d5db;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .action-icon:hover {
            color: #1f2937;
            background-color: #e5e7eb;
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

        /* User details row */
        .user-details-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .user-details-info {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .user-details-top {
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }

        .user-details-info .avatar {
            width: 120px;
            /* Size to match combined width of name, mention, and student_id */
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-details-info .info-text {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .user-details-info .primary-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .user-details-info .mention,
        .user-details-info .student-id {
            font-weight: 600;
            color: #1f2937;
            font-size: 0.75rem;
            min-width: 100px;
            /* Ensure consistent width for alignment */
        }

        .user-details-info .meta {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .user-details-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Status select styling */
        .status-select {
            padding: 4px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 14px;
            width: 120px;
            color: #1f2937;
        }

        .status-pending {
            background-color: #fefcbf;
            color: #854d0e;
        }

        .status-reviewed {
            background-color: #bfdbfe;
            color: #1e3a8a;
        }

        .status-resolved {
            background-color: #dcfce7;
            color: #15803d;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .status-select option {
            background-color: #fff;
            color: #1f2937;
        }

        .status-select option[value="pending"] {
            background-color: #fefcbf;
            color: #854d0e;
        }

        .status-select option[value="reviewed"] {
            background-color: #bfdbfe;
            color: #1e3a8a;
        }

        .status-select option[value="resolved"] {
            background-color: #dcfce7;
            color: #15803d;
        }

        .status-select option[value="rejected"] {
            background-color: #fee2e2;
            color: #b91c1c;
        }
    </style>

    <div class="bg-white rounded-lg shadow p-6">
        <!-- User Details -->
        <div class="user-details-row">
            <div class="user-details-info">
                <div class="user-details-top">
                    <img src="{{ $user->avatar_url }}" alt="Avatar" class="avatar">
                    <div class="info-text">
                        <div class="primary-info">
                            <span class="name text-3xl font-semibold">{{ $user->name }}</span>
                            <span class="mention">{{ '@' . $user->mention }}</span>
                            <span class="student-id">MSSV: {{ $user->student_id ?? 'Chưa cung cấp' }}</span>
                        </div>
                    </div>
                </div>
                <div class="meta">
                    Email: {{ $user->email }} |
                    Role: {{ $user->role }} |
                    Ngày sinh: {{ $user->dob ? $user->dob->format('d/m/Y') : 'Chưa cung cấp' }} |
                    Lớp: {{ $user->class ?? 'Chưa cung cấp' }} |
                    Ngành học: {{ $user->major ?? 'Chưa cung cấp' }} |
                    Khóa: {{ $user->course ?? 'Chưa cung cấp' }}
                </div>
            </div>
            <div class="user-details-actions">
                <a href="{{ route('admin.users.edit', $user->mention) }}" title="Sửa người dùng"
                    class="action-icon edit-icon">
                    <i class="fa-solid fa-pencil"></i>
                </a>
                <select class="status-select status-{{ $report ? $report->status : 'pending' }}" id="user-status"
                    data-user-id="{{ $user->id }}" data-current-status="{{ $report ? $report->status : 'pending' }}">
                    <option value="pending" {{ $report && $report->status == 'pending' ? 'selected' : '' }}>Đang chờ
                    </option>
                    <option value="reviewed" {{ $report && $report->status == 'reviewed' ? 'selected' : '' }}>Đã xem xét
                    </option>
                    <option value="resolved" {{ $report && $report->status == 'resolved' ? 'selected' : '' }}>Đã giải quyết
                    </option>
                    <option value="rejected" {{ $report && $report->status == 'rejected' ? 'selected' : '' }}>Đã từ chối
                    </option>
                </select>
            </div>
        </div>

        <hr class="my-6">

        <!-- User Posts -->
        <div>
            <h3 class="text-lg font-semibold mb-4">Bài viết của {{ $user->name }}</h3>
            <div class="divide-y">
                @forelse ($posts as $post)
                    <div class="flex items-center justify-between py-3">
                        <div class="flex items-center gap-3">
                            <div>
                                <div class="font-semibold">
                                    {{ $post->title }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    Đăng bởi: {{ $post->user->name ?? 'N/A' }} |
                                    {{ $post->created_at->diffForHumans() }} |
                                    Hashtags: {{ $post->hashtag ?? 'Không có' }} |
                                    Lượt thích: {{ $post->likes_count }} |
                                    Lượt chia sẻ: {{ $post->shares }} |
                                    Lượt xem: {{ $post->views }}
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
                                                <img src="{{ asset('storage/' . $m->media_path) }}" alt="Hình ảnh bài viết"
                                                    class="post-image" loading="lazy">
                                            @elseif ($m->media_type === 'video')
                                                <video class="post-video">
                                                    <source src="{{ asset('storage/' . $m->media_path) }}"
                                                        type="video/mp4">
                                                    Trình duyệt không hỗ trợ video.
                                                </video>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.posts.edit', $post->slug) }}" title="Sửa bài viết">
                                <i class="fa-solid fa-pencil action-icon edit-icon"></i>
                            </a>
                            <form action="{{ route('admin.posts.destroy', $post->slug) }}" method="POST"
                                onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Xóa bài viết" class="action-icon delete-icon">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 py-3">Người dùng này chưa có bài viết nào.</p>
                @endforelse
            </div>

            <!-- Pagination for Posts -->
            <div class="mt-4">
                {{ $posts->links() }}
            </div>

            <!-- Media Popup -->
            <div id="media-popup">
                <div class="popup-content">
                    <button class="close-popup">&times;</button>
                    <div id="popup-media"></div>
                </div>
            </div>

            <!-- Toast Notifications -->
            @if (session('success'))
                <div id="toast" class="toast">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Include jQuery and Font Awesome -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <script>
        document.addEventListener('DOMContentLoaded', () => {
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

            // Handle status change via AJAX
            $('#user-status').on('change', function() {
                const $select = $(this);
                const userId = $select.data('user-id');
                const newStatus = $select.val();
                const currentStatus = $select.data('current-status');

                // Update the class to reflect the new status color
                $select.removeClass('status-pending status-reviewed status-resolved status-rejected');
                $select.addClass(`status-${newStatus}`);
                $select.data('current-status', newStatus); // Update current status

                $.ajax({
                    url: '{{ route('admin.users.update-status') }}',
                    method: 'POST',
                    data: {
                        user_id: userId,
                        status: newStatus,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            const successToast = $('<div class="toast">' + response.message +
                                '</div>');
                            $('body').append(successToast);
                            setTimeout(() => {
                                successToast.addClass('show');
                            }, 100);
                            setTimeout(() => {
                                successToast.removeClass('show');
                                setTimeout(() => {
                                    successToast.remove();
                                }, 300);
                            }, 3000);
                        }
                    },
                    error: function(xhr) {
                        const errorMessage = xhr.responseJSON?.message ||
                            'Có lỗi xảy ra khi cập nhật trạng thái.';
                        const errorToast = $('<div class="toast error">' + errorMessage +
                            '</div>');
                        $('body').append(errorToast);
                        setTimeout(() => {
                            errorToast.addClass('show');
                        }, 100);
                        setTimeout(() => {
                            errorToast.removeClass('show');
                            setTimeout(() => {
                                errorToast.remove();
                            }, 300);
                        }, 3000);
                        // Revert to previous status
                        $select.val(currentStatus);
                        $select.removeClass(
                            'status-pending status-reviewed status-resolved status-rejected'
                            );
                        $select.addClass(`status-${currentStatus}`);
                    }
                });
            });
        });
    </script>
@endsection
