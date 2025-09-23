@extends('admin.layouts.app')

@section('title', 'Danh sách báo cáo sự cố')

@section('page-title', 'Danh sách báo cáo sự cố')

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
        display: inline-flex;
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
        flex-wrap: nowrap;
        align-items: flex-end;
        gap: 12px;
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
        min-width: 100px;
        flex: 1;
        height: 34px;
        box-sizing: border-box;
    }

    .filter-form button,
    .filter-form a.clear-btn {
        padding: 8px 16px;
        background-color: #3b82f6;
        color: white;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        flex: 0 0 auto;
        white-space: nowrap;
        height: 34px;
        line-height: 18px;
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
            min-width: 80px;
            font-size: 12px;
            height: 30px;
        }

        .filter-form button,
        .filter-form a.clear-btn {
            padding: 6px 12px;
            font-size: 12px;
            height: 30px;
            line-height: 18px;
        }

        .filter-form {
            gap: 8px;
        }
    }

    /* Select2 custom styles */
    .select2-container {
        flex: 1;
        min-width: 100px;
        max-width: 200px;
    }

    .select2-container .select2-selection--single {
        height: 34px;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .select2-container .select2-selection__rendered {
        line-height: 34px;
        padding-left: 8px;
        font-size: 14px;
    }

    .select2-container .select2-selection__arrow {
        height: 34px;
        width: 24px;
    }

    @media (max-width: 768px) {
        .select2-container .select2-selection--single {
            height: 30px;
        }

        .select2-container .select2-selection__rendered {
            line-height: 30px;
            font-size: 12px;
        }

        .select2-container .select2-selection__arrow {
            height: 30px;
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

    /* Fixed column widths */
    .excel-table th:nth-child(1),
    .excel-table td:nth-child(1) {
        width: 5%;
        text-align: center;
    }

    .excel-table th:nth-child(2),
    .excel-table td:nth-child(2) {
        width: 15%;
    }

    .excel-table th:nth-child(3),
    .excel-table td:nth-child(3) {
        width: 25%;
    }

    .excel-table th:nth-child(4),
    .excel-table td:nth-child(4) {
        width: 15%;
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

    /* Status badge styling */
    .status-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }

    .status-pending {
        background-color: #fefcbf;
        color: #854d0e;
    }

    .status-resolved {
        background-color: #dcfce7;
        color: #15803d;
    }
</style>

<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">Báo cáo sự cố</h2>
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-filter action-icon filter-toggle" title="Hiển thị bộ lọc"></i>
        </div>
    </div>

    <!-- Filter Form -->
    <form action="{{ route('admin.incident-reports.index') }}" method="GET" class="filter-form" id="filter-form">
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700">Trạng thái</label>
            <select name="status" id="status" class="mt-1 select2">
                <option value="">Tất cả</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Đang chờ</option>
                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Đã xử lý</option>
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
            <a href="{{ route('admin.incident-reports.index') }}" class="clear-btn">Xóa bộ lọc</a>
        </div>
    </form>
    <hr>

    <!-- Toast Notifications -->
    @if (session('success'))
        <div id="toast" class="toast">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div id="toast" class="toast error">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Reports Table -->
    <table class="excel-table">
        <thead>
            <tr>
                <th>STT</th>
                <th>Tiêu đề</th>
                <th>Mô tả</th>
                <th>Người gửi</th>
                <th>Ngày tạo</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reports as $index => $report)
                <tr>
                    <td>{{ $index + $reports->firstItem() }}</td>
                    <td>{{ $report->title }}</td>
                    <td>{{ Str::limit($report->description ?? 'Không có mô tả', 100) }}</td>
                    <td>{{ $report->user->name ?? 'N/A' }}</td>
                    <td>{{ $report->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <span class="status-badge status-{{ $report->status }}">
                            {{ $report->status == 'pending' ? 'Đang chờ' : 'Đã xử lý' }}
                        </span>
                    </td>
                    <td class="action-cell">
                        <button onclick="toggleReplyForm('reply-form-{{ $report->id }}')" class="action-icon" title="Phản hồi">
                            <i class="fa-solid fa-reply"></i>
                        </button>
                        <form action="{{ route('incident_reports.destroy', $report) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa báo cáo này?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-icon delete-icon" title="Xóa">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <tr id="reply-form-{{ $report->id }}" class="hidden">
                    <td colspan="7" class="p-6 bg-gray-50">
                        <div class="p-6 rounded-lg border border-gray-200">
                            <!-- Existing Replies -->
                            @if ($report->replies->count() > 0)
                                <div class="mb-6">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Phản hồi trước đó:</h4>
                                    <div class="space-y-3">
                                        @foreach ($report->replies as $reply)
                                            <div class="p-4 bg-gray-100 rounded-md">
                                                <p class="text-sm text-gray-800">{{ $reply->message }}</p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $reply->admin->name ?? 'Admin' }} - {{ $reply->created_at->format('d/m/Y H:i') }}
                                                </p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Reply Form -->
                            <form action="{{ route('admin.incident-reports.reply.store', $report) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="message-{{ $report->id }}" class="block text-sm font-medium text-gray-700">Phản hồi mới</label>
                                    <textarea name="message" id="message-{{ $report->id }}" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required></textarea>
                                    @error('message')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="flex space-x-3">
                                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">Gửi phản hồi</button>
                                    <button type="button" onclick="toggleReplyForm('reply-form-{{ $report->id }}')" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors duration-200">Hủy</button>
                                </div>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-gray-500 text-center py-3">Chưa có báo cáo sự cố nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $reports->links() }}
    </div>
</div>

<!-- JavaScript for filter toggle, toast, and date validation -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

<script>
    function toggleReplyForm(formId) {
        const form = document.getElementById(formId);
        form.classList.toggle('hidden');
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Initialize Select2 for status dropdown
        $('#status').select2({
            placeholder: 'Chọn trạng thái',
            allowClear: true,
            width: '100%'
        });

        // Persist selected status after form submission
        const selectedStatus = "{{ request('status') }}";
        if (selectedStatus) {
            $('#status').val(selectedStatus).trigger('change');
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

        // Handle date validation
        const filterFormElement = document.getElementById('filter-form');
        if (filterFormElement) {
            filterFormElement.addEventListener('submit', function (event) {
                const dateFrom = document.getElementById('date_from').value;
                const dateTo = document.getElementById('date_to').value;

                if (dateFrom && dateTo && new Date(dateFrom) > new Date(dateTo)) {
                    event.preventDefault();
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
@endsection