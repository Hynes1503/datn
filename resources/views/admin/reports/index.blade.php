@extends('admin.layouts.app')

@section('title', 'Quản lý báo cáo')
@section('page-title', 'Danh sách báo cáo')

@section('content')
    <style>
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

            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .action-icon:hover {
            color: #1f2937;
            background-color: #e5e7eb;
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
            display: none;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .filter-form.show {
            display: flex;
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
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
            vertical-align: middle;
        }

        .excel-table th {
            background-color: #f4f4f5;
            font-weight: 600;
            color: #1f2937;
        }

        .excel-table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .excel-table tr:hover {
            background-color: #e5e7eb;
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
            width: 5%;
            text-align: center;
        }

        .excel-table th:nth-child(2),
        .excel-table td:nth-child(2) {
            width: 20%;
        }

        .excel-table th:nth-child(3),
        .excel-table td:nth-child(3) {
            width: 25%;
        }

        .excel-table th:nth-child(4),
        .excel-table td:nth-child(4) {
            width: 25%;
        }

        .excel-table th:nth-child(5),
        .excel-table td:nth-child(5) {
            width: 15%;
        }

        .excel-table th:nth-child(6),
        .excel-table td:nth-child(6) {
            width: 10%;
            text-align: center;
        }

        .excel-table th:nth-child(7),
        .excel-table td:nth-child(7) {
            width: 10%;
            min-width: 100px;
            text-align: center;
        }

        /* Action cell styling */
        .action-cell {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            min-height: 40px;
        }

        .action-cell form {
            display: inline-flex;
            margin: 0;
        }

        /* Status select styling */
        .status-select {
            padding: 4px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 14px;
            width: 120px;
        }
    </style>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Báo cáo</h2>
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-filter action-icon filter-toggle" title="Hiển thị bộ lọc"></i>
            </div>
        </div>

        <!-- Filter Form -->
        <form action="{{ route('reports.index') }}" method="GET" class="filter-form">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Trạng thái</label>
                <select name="status" id="status" class="mt-1 select2">
                    <option value="">Tất cả</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div>
                <label for="reportable_type" class="block text-sm font-medium text-gray-700">Loại báo cáo</label>
                <select name="reportable_type" id="reportable_type" class="mt-1 select2">
                    <option value="">Tất cả</option>
                    <option value="user" {{ request('reportable_type') == 'user' ? 'selected' : '' }}>User</option>
                    <option value="post" {{ request('reportable_type') == 'post' ? 'selected' : '' }}>Post</option>
                    <option value="comment" {{ request('reportable_type') == 'comment' ? 'selected' : '' }}>Comment</option>
                </select>
            </div>
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700">Ngày từ</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="mt-1">
            </div>
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700">Ngày đến</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="mt-1">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit">Lọc</button>
                <a href="{{ route('reports.index') }}" class="clear-btn px-4 py-2 text-white rounded">Xóa bộ lọc</a>
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
                    <th>Người báo cáo</th>
                    <th>Đối tượng</th>
                    <th>Lý do</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $index => $report)
                    <tr>
                        <td>{{ $index + $reports->firstItem() }}</td>
                        <td>
                            @if($report->reporter)
                                <a href="{{ route('admin.users.show', $report->reporter->mention) }}" target="_blank">
                                    {{ $report->reporter->name ?? 'N/A' }}
                                </a>
                            @else
                                Ẩn danh
                            @endif
                        </td>
                        <td>
                            @if ($report->reportable_type === \App\Models\User::class)
                                <a href="{{ route('admin.users.show', $report->reportable->mention) }}" target="_blank">
                                    👤 User: {{ $report->reportable->name ?? '[Đã xoá]' }}
                                </a>
                            @elseif ($report->reportable_type === \App\Models\Post::class)
                                <a href="{{ route('posts.show', [$report->reportable->user->mention, $report->reportable->slug]) }}" target="_blank">
                                    📝 Post: {{ $report->reportable->title ?? '[Đã xoá]' }}
                                </a>
                            @elseif ($report->reportable_type === \App\Models\Comment::class)
                                <a href="{{ route('posts.show', [$report->reportable->post->user->mention, $report->reportable->post->slug]) }}#comment-{{ $report->reportable->id }}" target="_blank">
                                    💬 Comment: {{ Str::limit($report->reportable->content ?? '[Đã xoá]', 50) }}
                                </a>
                            @endif
                        </td>
                        <td>{{ $report->reason }}</td>
                        <td>
                            <select class="status-select" data-report-id="{{ $report->id }}">
                                <option value="pending" {{ $report->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="reviewed" {{ $report->status == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                                <option value="resolved" {{ $report->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="rejected" {{ $report->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </td>
                        <td>{{ $report->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <form action="{{ route('reports.destroy', $report) }}" method="POST"
                                  onsubmit="return confirm('Bạn có chắc muốn xóa báo cáo này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Xóa báo cáo" class="action-icon delete-icon">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-gray-500 text-center py-3">Chưa có báo cáo nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $reports->links() }}
        </div>

        <!-- JavaScript for auto-scrolling to comment -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Check if there's a hash in the URL (e.g., #comment-123)
                const hash = window.location.hash;
                if (hash && hash.startsWith('#comment-')) {
                    const commentElement = document.querySelector(hash);
                    if (commentElement) {
                        commentElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        // Optional: Highlight the comment
                        commentElement.style.transition = 'background-color 0.5s ease';
                        commentElement.style.backgroundColor = '#fefcbf';
                        setTimeout(() => {
                            commentElement.style.backgroundColor = 'transparent';
                        }, 2000);
                    }
                }
            });
        </script>
    </div>

    <!-- Include jQuery, Select2, and Font Awesome -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize Select2 for filter dropdowns
            $('#status').select2({
                placeholder: 'Chọn trạng thái',
                allowClear: true,
                width: '100%'
            });

            $('#reportable_type').select2({
                placeholder: 'Chọn loại báo cáo',
                allowClear: true,
                width: '100%'
            });

            // Handle Select2 value persistence after form submission
            const selectedStatus = "{{ request('status') }}";
            if (selectedStatus) {
                $('#status').val(selectedStatus).trigger('change');
            }

            const selectedType = "{{ request('reportable_type') }}";
            if (selectedType) {
                $('#reportable_type').val(selectedType).trigger('change');
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

            // Handle status change via AJAX
            $('.status-select').on('change', function () {
                const reportId = $(this).data('report-id');
                const newStatus = $(this).val();

                $.ajax({
                    url: '{{ route("reports.updateStatus") }}',
                    type: 'PATCH',
                    data: {
                        report_id: reportId,
                        status: newStatus,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        const toast = $('<div class="toast">' + response.message + '</div>');
                        $('body').append(toast);
                        setTimeout(() => {
                            toast.addClass('show');
                        }, 100);
                        setTimeout(() => {
                            toast.removeClass('show');
                            setTimeout(() => {
                                toast.remove();
                            }, 300);
                        }, 3000);
                    },
                    error: function (xhr) {
                        alert('Cập nhật trạng thái thất bại: ' + (xhr.responseJSON?.message || 'Lỗi không xác định'));
                        $(this).val($(this).data('current-status')); // Revert to previous status
                    }
                });
            });
        });
    </script>
@endsection