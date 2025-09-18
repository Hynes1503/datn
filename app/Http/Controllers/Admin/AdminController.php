<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Post;
use App\Models\Building;
use Illuminate\Support\Facades\DB;
class AdminController extends Controller
{
public function index(Request $request)
    {
        $type = $request->get('type', 'month'); // default = month

        // Thống kê số lượng
        $totalUsers     = User::count();
        $totalPosts     = Post::count();
        $totalBuildings = Building::count();
        $unreadNotifications = DB::table('notifications')->whereNull('read_at')->count();

        // Xử lý dữ liệu thống kê bài viết
        $labels = [];
        $data   = [];

        if ($type === 'day') {
            // 7 ngày gần nhất
            $posts = Post::selectRaw('DATE(created_at) as day, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('count', 'day')
                ->toArray();

            foreach (range(6, 0) as $i) {
                $date = now()->subDays($i)->toDateString();
                $labels[] = now()->subDays($i)->format('d/m');
                $data[]   = $posts[$date] ?? 0;
            }

        } elseif ($type === 'year') {
            // 5 năm gần nhất
            $posts = Post::selectRaw('YEAR(created_at) as year, COUNT(*) as count')
                ->where('created_at', '>=', now()->subYears(5))
                ->groupBy('year')
                ->orderBy('year')
                ->pluck('count', 'year')
                ->toArray();

            foreach (range(now()->year - 4, now()->year) as $year) {
                $labels[] = $year;
                $data[]   = $posts[$year] ?? 0;
            }

        } else {
            // Theo tháng (12 tháng gần nhất)
            $posts = Post::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->where('created_at', '>=', now()->subYear())
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month')
                ->toArray();

            foreach (range(1, 12) as $m) {
                $labels[] = "Th$m";
                $data[]   = $posts[$m] ?? 0;
            }
        }

        $latestUsers = User::orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalPosts',
            'totalBuildings',
            'unreadNotifications',
            'labels',
            'data',
            'latestUsers',
            'type'
        ));
    }

    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            if (Auth::user()->role === 'Admin') {
                return redirect()->route('admin.dashboard');
            }
            Auth::logout();
            return back()->withErrors([
                'email' => 'Bạn không có quyền truy cập admin.',
            ]);
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng.',
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }
}
