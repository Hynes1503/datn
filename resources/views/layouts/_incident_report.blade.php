<button type="button" id="openIncidentReportModal"
    class="flex justify-between items-center w-full px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 border-b border-gray-100 transition-colors">
    <span>Báo cáo sự cố</span>
    <i class="fa-solid fa-triangle-exclamation text-red-500"></i>
</button>

<!-- Modal -->
<div id="incidentReportModal"
    class="fixed inset-0 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white text-black w-full max-w-2xl rounded-2xl shadow-2xl border border-gray-200 p-0 flex flex-col">
        
        <!-- Header -->
        <div class="flex justify-between items-center border-b border-gray-200 px-4 py-3">
            <h2 class="text-base font-semibold">Báo cáo sự cố</h2>
            <button id="closeIncidentReportModal"
                class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <!-- Tabs -->
        <div class="flex border-b border-gray-200">
            <button id="tabReport" class="flex-1 px-4 py-2 text-sm font-medium text-center hover:bg-gray-100 active-tab">
                Báo cáo sự cố
            </button>
            <button id="tabReplies" class="flex-1 px-4 py-2 text-sm font-medium text-center hover:bg-gray-100">
                Phản hồi từ Admin
            </button>
        </div>

        <!-- Body -->
        <div class="p-4">
            <!-- Tab 1: Form báo cáo -->
            <div id="tabReportContent">
                <form id="incidentReportForm" method="POST" action="{{ route('incident_reports.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium mb-1">Tiêu đề</label>
                        <input type="text" name="title"
                            class="w-full bg-white border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-black focus:outline-none"
                            placeholder="Nhập tiêu đề..." required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Mô tả</label>
                        <textarea name="description" rows="4"
                            class="w-full bg-white border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-black focus:outline-none"
                            placeholder="Mô tả chi tiết sự cố..." required></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" id="closeIncidentReportModal2"
                            class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">
                            Hủy
                        </button>
                        <button type="submit"
                            class="bg-black text-white px-4 py-2 text-sm rounded-xl hover:bg-gray-800">
                            Gửi báo cáo
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tab 2: Phản hồi từ Admin -->
            <div id="tabRepliesContent" class="hidden">
                @php
                    $userReports = \App\Models\IncidentReport::with('replies.admin')
                        ->where('user_id', Auth::id())
                        ->latest()
                        ->get();
                @endphp

                @if($userReports->count())
                    <ul class="space-y-4 max-h-80 overflow-y-auto pr-2">
                        @foreach($userReports as $report)
                            <li class="p-4 border rounded-xl bg-gray-50 relative">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-semibold text-sm">{{ $report->title }}</p>
                                        <p class="text-xs text-gray-500 mb-2">{{ $report->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <form action="{{ route('incident_reports.destroy', $report->id) }}" method="POST" class="ml-2"
                                        onsubmit="return confirm('Bạn có chắc muốn xóa báo cáo này không?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm absolute top-4 right-4">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                
                                @if($report->replies->count())
                                    <ul class="space-y-2">
                                        @foreach($report->replies as $reply)
                                            <li class="p-3 bg-white border rounded-lg flex justify-between items-start">
                                                <div>
                                                    <p class="text-sm text-gray-700">{{ $reply->message }}</p>
                                                    <p class="text-xs text-gray-400 mt-1">
                                                        {{ $reply->admin->name }} - {{ $reply->created_at->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-sm text-gray-500 italic">Chưa có phản hồi</p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-500">Bạn chưa gửi báo cáo sự cố nào.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("incidentReportModal");
    const openBtn = document.getElementById("openIncidentReportModal");
    const closeBtn = document.getElementById("closeIncidentReportModal");
    const closeBtn2 = document.getElementById("closeIncidentReportModal2");

    const tabReport = document.getElementById("tabReport");
    const tabReplies = document.getElementById("tabReplies");
    const tabReportContent = document.getElementById("tabReportContent");
    const tabRepliesContent = document.getElementById("tabRepliesContent");

    function openModal() {
        modal.classList.remove("hidden");
        switchTab("report");
    }
    function closeModal() {
        modal.classList.add("hidden");
    }

    function switchTab(tab) {
        if (tab === "report") {
            tabReportContent.classList.remove("hidden");
            tabRepliesContent.classList.add("hidden");
            tabReport.classList.add("active-tab");
            tabReplies.classList.remove("active-tab");
        } else {
            tabRepliesContent.classList.remove("hidden");
            tabReportContent.classList.add("hidden");
            tabReplies.classList.add("active-tab");
            tabReport.classList.remove("active-tab");
        }
    }

    openBtn.addEventListener("click", openModal);
    closeBtn.addEventListener("click", closeModal);
    if (closeBtn2) closeBtn2.addEventListener("click", closeModal);

    tabReport.addEventListener("click", () => switchTab("report"));
    tabReplies.addEventListener("click", () => switchTab("replies"));
});
</script>

<style>
    .active-tab {
        border-bottom: 2px solid black;
        color: black;
        font-weight: 600;
    }
</style>

@if(session('success'))
    <script>
        alert("{{ session('success') }}");
    </script>
@endif