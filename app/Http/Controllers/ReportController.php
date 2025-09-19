<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class ReportController extends Controller
{
    /**
     * Danh sách report
     */
    public function index(Request $request)
    {
        $reports = Report::with(['reporter', 'reportable'])->latest()->paginate(20);

        if ($request->wantsJson()) {
            return response()->json($reports);
        }

        return view('admin.reports.index', compact('reports'));
    }

    /**
     * Chi tiết report
     */
    public function show(Request $request, Report $report)
    {
        $report->load(['reporter', 'reportable']);

        if ($request->wantsJson()) {
            return response()->json($report);
        }

        return view('admin.reports.show', compact('report'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'reportable_id' => 'required|integer',
            'reportable_type' => 'required|string|in:user,post,comment',
            'reason' => 'required|string|max:1000',
        ]);

        $map = [
            'user' => \App\Models\User::class,
            'post' => \App\Models\Post::class,
            'comment' => \App\Models\Comment::class,
        ];

        Report::create([
            'reporter_id' => Auth::id(),
            'reportable_id' => $request->reportable_id,
            'reportable_type' => $map[$request->reportable_type],
            'reason' => $request->reason,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Báo cáo đã được gửi, admin sẽ xem xét sớm.'
        ]);
    }
    /**
     * Update trạng thái report
     */
    public function update(Request $request, Report $report)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewed,resolved,rejected',
        ]);

        $report->update(['status' => $request->status]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công!',
                'report' => $report
            ]);
        }

        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công!');
    }

    /**
     * Xóa report
     */
    public function destroy(Request $request, Report $report)
    {
        $report->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa report!'
            ]);
        }

        return redirect()->route('admin.reports.index')->with('success', 'Đã xóa report!');
    }
}
