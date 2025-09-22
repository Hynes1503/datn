<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $suggestedUsers = collect();

            if (Auth::check()) {
                $user = Auth::user();

                $suggestedUsers = User::where('id', '!=', $user->id)
                    ->where('role', '!=', 'Admin') // loại Admin
                    ->whereDoesntHave('reports', function ($q) {
                        $q->where('status', 'resolved'); // loại user bị khóa
                    })
                    ->take(5)
                    ->get();
            }

            // Gửi biến sang tất cả view
            $view->with('suggestedUsers', $suggestedUsers);
        });
    }
}
