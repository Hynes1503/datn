@extends('admin.layouts.app')

@section('title', 'Quản lý bình luận')
@section('page-title', 'Danh sách bình luận')

@section('content')
    <style>
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
            text-decoration: none; /* Ensure no underline on links */
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

        .filter-toggle {
            cursor: pointer;
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

        /* Excel-like table styles */
        .excel-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            background-color: #fff;
        }

        .excel-table th,
        .excel-table td {
            border: 1px solid #d1d5db; /* Thin grid lines like Excel */
            padding: 8px;
            text-align: left;
            vertical-align: middle;
        }

        .excel-table th {
            background-color: #f4f4f5; /* Light gray header like Excel */
            font-weight: 600;
            color: #1f2937;
        }

        .excel-table tr:nth-child(even) {
            background-color: #f9fafb; /* Alternating row colors */
        }

        .excel-table tr:hover {
            background-color: #e5e7eb; /* Hover effect */
        }

        .excel-table td a {
            color: #3b82f6;
            text-decoration: none;
        }

        .excel-table td a:hover {
            text-decoration: underline;
        }

        /* Fixed column widths for Excel-like appearance */
        .excel-table th:nth-child(1),
        .excel-table td:nth-child(1) {
            width: 5%; /* STT */
            text-align: center;
        }

        .excel-table th:nth-child(2),
        .excel-table td:nth-child(2) {
            width: 30%; /* Nội dung */
        }

        .excel-table th:nth-child(3),
        .excel-table td:nth-child(3) {
            width: 15%; /* Người dùng */
        }

        .excel-table th:nth-child(4),
        .excel-table td:nth-child(4) {
            width: 30%; /* Bài viết */
        }

        .excel-table th:nth-child(5),
        .excel-table td:nth-child(5) {
            width: 15%; /* Ngày tạo */
        }

        .excel-table th:nth-child(6),
        .excel-table td:nth-child(6) {
            width: 10%; /* Hành động */
            min-width: 100px; /* Ensure enough space for icons */
            text-align: center;
        }

        /* Action cell styling */
        .action-cell {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            min-height: 40px; /* Prevent collapse */
        }

        /* Ensure form doesn't affect layout */
        .action-cell form {
            display: inline-flex;
            margin: 0;
        }
    </style>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Bình luận</h2>
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-filter action-icon filter-toggle" title="Hiển thị bộ lọc"></i>
            </div>
        </div>

        <!-- Filter Form -->
        <form action="{{ route('admin.comments.index') }}" method="GET" class="filter-form">
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700">Người dùng</label>
                <select name="user_id" id="user_id" class="mt-1 select2">
                    <option value="">Tất cả người dùng</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="post_id" class="block text-sm font-medium text-gray-700">Bài viết</label>
                <select name="post_id" id="post_id" class="mt-1 select2">
                    <option value="">Tất cả bài viết</option>
                    @foreach ($posts as $post)
                        <option value="{{ $post->id }}" {{ request('post_id') == $post->id ? 'selected' : '' }}>
                            {{ $post->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700">Ngày tạo từ</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="mt-1">
            </div>
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700">Ngày tạo đến</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="mt-1">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit">Lọc</button>
                <a href="{{ route('admin.comments.index') }}" class="clear-btn px-4 py-2 text-white rounded">Xóa bộ lọc</a>
            </div>
        </form>
        <hr>

        @if(session('success'))
            <div id="toast" class="toast">
                {{ session('success') }}
            </div>
        @endif

        <table class="excel-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Nội dung</th>
                    <th>Người dùng</th>
                    <th>Bài viết</th>
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($comments as $index => $comment)
                    <tr>
                        <td>{{ $index + $comments->firstItem() }}</td>
                        <td>{{ Str::limit($comment->content, 50) }}</td>
                        <td>
                            @if($comment->user)
                                <a href="{{ route('admin.users.show', $comment->user->mention) }}" target="_blank">
                                    {{ $comment->user->name }}
                                </a>
                            @else
                                Ẩn danh
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('posts.show', [$comment->post->user->mention, $comment->post->slug]) }}" target="_blank">
                                {{ $comment->post->title }}
                            </a>
                        </td>
                        <td>{{ $comment->created_at->format('d/m/Y H:i') }}</td>
                        <td class="action-cell">
                            <a href="{{ route('admin.comments.edit', $comment) }}" title="Sửa bình luận">
                                <i class="fa-solid fa-pencil action-icon edit-icon"></i>
                            </a>
                            <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST"
                                  onsubmit="return confirm('Bạn có chắc muốn xóa bình luận này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Xóa bình luận" class="action-icon delete-icon">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-gray-500 text-center py-3">Chưa có bình luận nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $comments->links() }}
        </div>
    </div>

    <!-- Include jQuery, Select2, and Font Awesome -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize Select2 for user and post dropdowns
            $('#user_id').select2({
                placeholder: 'Chọn người dùng',
                allowClear: true,
                width: '100%'
            });

            $('#post_id').select2({
                placeholder: 'Chọn bài viết',
                allowClear: true,
                width: '100%'
            });

            // Handle Select2 value persistence after form submission
            const selectedUser = "{{ request('user_id') }}";
            if (selectedUser) {
                $('#user_id').val(selectedUser).trigger('change');
            }

            const selectedPost = "{{ request('post_id') }}";
            if (selectedPost) {
                $('#post_id').val(selectedPost).trigger('change');
            }

            // Handle filter form toggle
            const filterToggle = document.querySelector('.filter-toggle');
            const filterForm = document.querySelector('.filter-form');
            filterToggle.addEventListener('click', () => {
                filterForm.classList.toggle('show');
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