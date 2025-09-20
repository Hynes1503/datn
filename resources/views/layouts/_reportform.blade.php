<!-- Modal Report -->
<div id="reportModal" class="hidden fixed inset-0 bg-black/30 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-lg w-full max-w-md p-4">
        <form id="reportForm" class="flex flex-col gap-4">
            @csrf
            <input type="hidden" name="reportable_id" id="reportable_id">
            <input type="hidden" name="reportable_type" id="reportable_type">

            <!-- Tiêu đề -->
            <h2 class="text-lg font-semibold text-gray-800">Báo cáo nội dung</h2>

            <!-- Lý do -->
            <div>
                <label for="reason" class="block text-sm text-gray-600 mb-1">Lý do</label>
                <textarea name="reason" id="reason" rows="3"
                    class="w-full text-sm border rounded-lg p-2 focus:ring-1 focus:ring-black focus:border-black resize-none"></textarea>
            </div>

            <!-- Action row -->
            <div class="flex justify-end gap-2 border-t pt-3">
                <button type="button" onclick="closeReportModal()"
                    class="px-4 py-1.5 text-sm rounded-full border border-gray-300 text-gray-700 hover:bg-gray-100">
                    Hủy
                </button>
                <button type="submit"
                    class="px-4 py-1.5 bg-black text-white text-sm rounded-full hover:bg-gray-900 transition">
                    Gửi báo cáo
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openReportModal(type, id) {
        document.getElementById("reportModal").classList.remove("hidden");
        document.getElementById("reportable_id").value = id;
        document.getElementById("reportable_type").value = type;
    }

    function closeReportModal() {
        document.getElementById("reportModal").classList.add("hidden");
    }

    document.getElementById("reportForm").addEventListener("submit", function(e) {
        e.preventDefault();

        fetch("{{ route('reports.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    reportable_id: document.getElementById("reportable_id").value,
                    reportable_type: document.getElementById("reportable_type").value,
                    reason: this.reason.value
                })
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                closeReportModal();
                this.reset();
            })
            .catch(err => {
                console.error(err);
                alert("Có lỗi xảy ra, vui lòng thử lại!");
            });
    });
</script>
