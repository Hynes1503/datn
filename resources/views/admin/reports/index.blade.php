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
            display: inline-flex; /* Ensure icon is displayed properly */
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

        .toast.error {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        /* Filter form styles */
        .filter-form {
            display: none;
            flex-wrap: nowrap; /* Prevent wrapping to keep all elements in one row */
            align-items: flex-end;
            gap: 12px; /* Reduced gap for tighter spacing */
            margin-bottom: 16px;
            width: 100%;
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
            min-width: 100px; /* Minimum width to prevent collapse */
            flex: 1; /* Allow inputs to shrink proportionally */
            height: 34px; /* Consistent height for all inputs and selects */
            box-sizing: border-box; /* Ensure padding is included in height/width */
        }

        .filter-form button,
        .filter-form a.clear-btn {
            padding: 8px 16px;
            background-color: #3b82f6;
            color: white;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            flex: 0 0 auto; /* Prevent buttons from shrinking */
            white-space: nowrap; /* Prevent text wrapping in buttons */
            height: 34px; /* Match height of inputs and selects */
            line-height: 18px; /* Center text vertically */
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

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .filter-form select,
            .filter-form input {
                min-width: 80px; /* Smaller minimum width for mobile */
                font-size: 12px; /* Slightly smaller font for mobile */
                height: 30px; /* Slightly smaller height for mobile */
            }

            .filter-form button,
            .filter-form a.clear-btn {
                padding: 6px 12px; /* Smaller padding for buttons */
                font-size: 12px; /* Smaller font for buttons */
                height: 30px; /* Match mobile height of inputs */
                line-height: 18px; /* Adjust vertical alignment */
            }

            .filter-form {
                gap: 8px; /* Even tighter gap on smaller screens */
            }
        }

        /* Select2 custom styles */
        .select2-container {
            flex: 1; /* Allow Select2 to shrink proportionally */
            min-width: 100px; /* Match min-width of inputs */
            max-width: 200px; /* Maximum width to prevent over-expansion */
        }

        .select2-container .select2-selection--single {
            height: 34px; /* Match height of inputs */
            border: 1px solid #d1d5db;
            border-radius: 4px;
            box-sizing: border-box; /* Ensure padding is included */
        }

        .select2-container .select2-selection__rendered {
            line-height: 34px; /* Center text vertically */
            padding-left: 8px;
            font-size: 14px; /* Match font size of inputs */
        }

        .select2-container .select2-selection__arrow {
            height: 34px; /* Match height */
            width: 24px; /* Ensure consistent arrow size */
        }

        /* Responsive Select2 adjustments */
        @media (max-width: 768px) {
            .select2-container .select2-selection--single {
                height: 30px; /* Match mobile height of inputs */
            }

            .select2-container .select2-selection__rendered {
                line-height: 30px; /* Adjust for mobile */
                font-size: 12px; /* Match mobile font size */
            }

            .select2-container .select2-selection__arrow {
                height: 30px; /* Match mobile height */
            }
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
            color: #1f2937; /* Default text color */
        }

        /* Status-specific background colors */
        .status-pending {
            background-color: #fefcbf; /* Light yellow for pending */
            color: #854d0e; /* Darker text for contrast */
        }

        .status-reviewed {
            background-color: #bfdbfe; /* Light blue for reviewed */
            color: #1e3a8a; /* Darker text for contrast */
        }

        .status-resolved {
            background-color: #dcfce7; /* Light green for resolved */
            color: #15803d; /* Darker text for contrast */
        }

        .status-rejected {
            background-color: #fee2e2; /* Light red for rejected */
            color: #b91c1c; /* Darker text for contrast */
        }

        /* Ensure select options inherit the same colors */
        .status-select option {
            background-color: #fff; /* Default background for options */
            color: #1f2937; /* Default text color for options */
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

        /* Font Awesome icon styling */
        .fa-icon {
            margin-right: 4px;
            font-size: 14px;
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
        <form action="{{ route('reports.index') }}" method="GET" class="filter-form" id="filter-form">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Trạng thái</label>
                <select name="status" id="status" class="mt-1 select2">
                    <option value="">Tất cả</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Đang chờ</option>
                    <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Đã xem xét</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Đã giải quyết</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
                </select>
            </div>
            <div>
                <label for="reportable_type" class="block text-sm font-medium text-gray-700">Đối tượng</label>
                <select name="reportable_type" id="reportable_type" class="mt-1 select2">
                    <option value="">Tất cả</option>
                    <option value="user" {{ request('reportable_type') == 'user' ? 'selected' : '' }}>Người dùng</option>
                    <option value="post" {{ request('reportable_type') == 'post' ? 'selected' : '' }}>Bài viết</option>
                    <option value="comment" {{ request('reportable_type') == 'comment' ? 'selected' : '' }}>Bình luận</option>
                </select>
            </div>
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700">Ngày bắt đầu</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="mt-1">
            </div>
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700">Ngày kết thúc</label>
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
                                    <i class="fa-solid fa-user fa-icon"></i> Người dùng: {{ $report->reportable->name ?? '[Đã xoá]' }}
                                </a>
                            @elseif ($report->reportable_type === \App\Models\Post::class)
                                <a href="{{ route('posts.show', [$report->reportable->user->mention, $report->reportable->slug]) }}" target="_blank">
                                    <i class="fa-solid fa-file-alt fa-icon"></i> Bài viết: {{ $report->reportable->title ?? '[Đã xoá]' }}
                                </a>
                            @elseif ($report->reportable_type === \App\Models\Comment::class)
                                <a href="{{ route('posts.show', [$report->reportable->post->user->mention, $report->reportable->post->slug]) }}#comment-{{ $report->reportable->id }}" target="_blank">
                                    <i class="fa-solid fa-comment fa-icon"></i> Bình luận: {{ Str::limit($report->reportable->content ?? '[Đã xoá]', 50) }}
                                </a>
                            @endif
                        </td>
                        <td>{{ $report->reason }}</td>
                        <td>
                            <select class="status-select status-{{ $report->status }}" data-report-id="{{ $report->id }}" data-current-status="{{ $report->status }}">
                                <option value="pending" {{ $report->status == 'pending' ? 'selected' : '' }}>Đang chờ</option>
                                <option value="reviewed" {{ $report->status == 'reviewed' ? 'selected' : '' }}>Đã xem xét</option>
                                <option value="resolved" {{ $report->status == 'resolved' ? 'selected' : '' }}>Đã giải quyết</option>
                                <option value="rejected" {{ $report->status == 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
                            </select>
                        </td>
                        <td>{{ $report->created_at->format('d/m/Y H:i') }}</td>
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

        <!-- JavaScript for auto-scrolling to comment and status color updates -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Check if there's a hash in the URL (e.g., #comment-123)
                const hash = window.location.hash;
                if (hash && hash.startsWith('#comment-')) {
                    const commentElement = document.querySelector(hash);
                    if (commentElement) {
                        commentElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        commentElement.style.transition = 'background-color 0.5s ease';
                        commentElement.style.backgroundColor = '#fefcbf';
                        setTimeout(() => {
                            commentElement.style.backgroundColor = 'transparent';
                        }, 2000);
                    }
                }

                // Initialize Select2 for filter dropdowns
                $('#status').select2({
                    placeholder: 'Chọn trạng thái',
                    allowClear: true,
                    width: '100%'
                });

                $('#reportable_type').select2({
                    placeholder: 'Chọn Đối tượng',
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
                if (filterToggle && filterForm) {
                    filterToggle.addEventListener('click', () => {
                        filterForm.classList.toggle('show');
                    });
                } else {
                    console.error('Filter toggle or form not found in the DOM');
                }

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
                    const $select = $(this);

                    // Update the class to reflect the new status color
                    $select.removeClass('status-pending status-reviewed status-resolved status-rejected');
                    $select.addClass(`status-${newStatus}`);
                    $select.data('current-status', newStatus); // Update current status

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
                            $select.val($select.data('current-status')); // Revert to previous status
                            $select.removeClass('status-pending status-reviewed status-resolved status-rejected');
                            $select.addClass(`status-${$select.data('current-status')}`);
                        }
                    });
                });

                // Handle date validation
                const filterFormElement = document.getElementById('filter-form');
                if (filterFormElement) {
                    filterFormElement.addEventListener('submit', function (event) {
                        const dateFrom = document.getElementById('date_from').value;
                        const dateTo = document.getElementById('date_to').value;

                        if (dateFrom && dateTo && new Date(dateFrom) > new Date(dateTo)) {
                            event.preventDefault(); // Prevent form submission
                            const errorToast = $('<div class="toast error">Ngày bắt đầu không được muộn hơn ngày kết thúc.</div>');
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
                        }
                    });
                } else {
                    console.error('Filter form element not found');
                }
            });
        </script>
    </div>

    <!-- Include jQuery, Select2, and Font Awesome -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
@endsection