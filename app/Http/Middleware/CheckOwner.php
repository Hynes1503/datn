<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckOwner
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Nếu route có param "user"
        if ($request->route('user')) {
            $routeUser = $request->route('user');
            if ($routeUser->id !== $user->id) {
                return redirect()->route('home')->with('error', 'Bạn không có quyền thực hiện hành động này.');
            }
        }

        if ($request->route('post')) {
            $post = $request->route('post');
            if ($post->user_id !== $user->id) {
                return redirect()->route('home')->with('error', 'Bạn không có quyền thực hiện hành động này.');
            }
        }

        return $next($request);
    }
}
