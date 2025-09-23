<?php

namespace App\Http\Controllers;

use App\Models\IncidentReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidentReportController extends Controller
{
    public function index(Request $request)
    {
        // Only allow admins to access this page
        if (Auth::user()->role !== 'Admin') {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        $query = IncidentReport::with(['user', 'replies.admin'])->latest();

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $reports = $query->paginate(10)->appends($request->query());

        return view('admin.incident-reports.index', compact('reports'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        IncidentReport::create([
            'user_id'     => Auth::id(),
            'title'       => $request->title,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'gửi báo cáo thành công');
    }

    public function destroy(IncidentReport $report)
    {
        // Cho phép cả admin và user xóa (user chỉ xóa báo cáo của mình)
        if ($report->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Bạn không có quyền xóa báo cáo này.');
        }

        $report->delete();

        return redirect()->back()->with('success', 'Đã xóa báo cáo và các phản hồi liên quan.');
    }
}
