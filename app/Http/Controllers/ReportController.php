<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Initialize query for reports
        $query = Report::query()->with(['reporter', 'reportable']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by reportable type
        if ($request->filled('reportable_type')) {
            $reportableType = $request->reportable_type;
            $modelMap = [
                'user' => \App\Models\User::class,
                'post' => \App\Models\Post::class,
                'comment' => \App\Models\Comment::class,
            ];

            if (array_key_exists($reportableType, $modelMap)) {
                $query->where('reportable_type', $modelMap[$reportableType]);
            }
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Order by latest and paginate
        $reports = $query->latest()->paginate(10);

        // Pass data to the view
        return view('admin.reports.index', compact('reports'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'reportable_id' => 'required|integer',
            'reportable_type' => 'required|string|in:user,post,comment',
        ]);

        $map = [
            'user' => \App\Models\User::class,
            'post' => \App\Models\Post::class,
            'comment' => \App\Models\Comment::class,
        ];

        // Kiểm tra xem đã report chưa
        $exists = Report::where('reporter_id', Auth::id())
            ->where('reportable_id', $request->reportable_id)
            ->where('reportable_type', $map[$request->reportable_type])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã báo cáo rồi.'
            ]);
        }

        // Nếu chưa có thì tạo mới
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

    public function show(Report $report)
    {
        // Load related data for the report
        $report->load(['reporter', 'reportable']);
        return view('admin.reports.show', compact('report'));
    }
    public function updateStatus(Request $request)
    {
        $request->validate([
            'report_id' => 'required|exists:reports,id',
            'status' => 'required|in:pending,reviewed,resolved,rejected',
        ]);

        $report = Report::findOrFail($request->report_id);
        $report->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công.',
        ]);
    }
    public function destroy(Report $report)
    {
        $report->delete();
        return redirect()->route('reports.index')->with('success', 'Báo cáo đã được xóa thành công.');
    }
}
