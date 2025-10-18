<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Post;
use App\Models\Building;
use Illuminate\Support\Facades\DB;
use App\Models\Comment;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'month');

        $totalUsers = User::count();
        $totalPosts = Post::count();
        $totalComments = Comment::count();
        $unreadNotifications = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;

        $latestUsers = User::latest()->take(5)->get();

        if ($type === 'day') {
            $labels = collect(range(6, 0))->map(fn($i) => Carbon::now()->subDays($i)->format('d/m'));
            $data = $labels->map(function ($label) {
                $date = Carbon::createFromFormat('d/m', $label)->setYear(now()->year);
                return Post::whereDate('created_at', $date)->count();
            });
        } elseif ($type === 'year') {
            $labels = collect(range(now()->year - 5, now()->year));
            $data = $labels->map(fn($year) => Post::whereYear('created_at', $year)->count());
        } else {
            $labels = collect(range(1, 12))->map(fn($m) => "Tháng $m");
            $data = collect(range(1, 12))->map(
                fn($m) => Post::whereYear('created_at', now()->year)
                    ->whereMonth('created_at', $m)
                    ->count()
            );
        }

        $topPostsQuery = Post::select('title', 'views')
            ->orderByDesc('views')
            ->limit(5)
            ->get();

        $topPosts = [
            'labels' => $topPostsQuery->pluck('title'),
            'data'   => $topPostsQuery->pluck('views'),
        ];

        $usersPerMonthQuery = User::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->whereYear('created_at', now()->year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('MONTH(created_at)'))
            ->get();

        $usersPerMonth = [
            'labels' => $usersPerMonthQuery->pluck('month')->map(fn($m) => str_pad($m, 2, '0', STR_PAD_LEFT)),
            'data'   => $usersPerMonthQuery->pluck('count'),
        ];

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalPosts',
            'totalComments',
            'unreadNotifications',
            'latestUsers',
            'labels',
            'data',
            'type',
            'topPosts',
            'usersPerMonth'
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