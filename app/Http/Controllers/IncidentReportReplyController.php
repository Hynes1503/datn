<?php

namespace App\Http\Controllers;

use App\Models\IncidentReport;
use App\Models\IncidentReportReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidentReportReplyController extends Controller
{
    public function store(Request $request, IncidentReport $report)
    {
        // Only allow admins to create replies
        if (Auth::user()->role !== 'Admin') {
            abort(403, 'Bạn không có quyền gửi phản hồi.');
        }

        $request->validate([
            'message' => 'required|string',
        ]);

        IncidentReportReply::create([
            'incident_report_id' => $report->id,
            'admin_id' => Auth::id(),
            'message' => $request->message,
        ]);

        // Update the report's status to 'resolved'
        $report->update(['status' => 'resolved']);

        return redirect()->back()->with('success', 'Gửi phản hồi thành công');
    }
    public function destroy(IncidentReportReply $reply)
    {
        // Nếu muốn giới hạn: chỉ người tạo phản hồi hoặc admin mới được xóa
        if ($reply->admin_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Bạn không có quyền xóa phản hồi này.');
        }

        $reply->delete();

        return redirect()->back()->with('success', 'Đã xóa phản hồi.');
    }
}
