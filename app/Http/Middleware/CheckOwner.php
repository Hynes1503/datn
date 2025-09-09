<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CheckOwner
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Nếu route có param "user"
        if ($request->route('user')) {
            $routeUser = $request->route('user');

            // Kiểm tra xem $routeUser có phải là đối tượng User hay không
            if (!($routeUser instanceof User)) {
                // Nếu là chuỗi, tìm User theo mention
                $routeUser = User::where('mention', $routeUser)->firstOrFail();
            }

            if ($routeUser->id !== $user->id) {
                return redirect()->route('home')->with('error', 'Bạn không có quyền thực hiện hành động này.');
            }
        }

        // Nếu route có param "post"
        if ($request->route('post')) {
            $post = $request->route('post');
            if ($post->user_id !== $user->id) {
                return redirect()->route('home')->with('error', 'Bạn không có quyền thực hiện hành động này.');
            }
        }

        return $next($request);
    }
}