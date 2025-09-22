<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\FollowNotification;

class FollowController extends Controller
{
    public function follow(User $user)
    {
        $authUser = Auth::user();

        // Kiểm tra đăng nhập
        if (!$authUser) {
            return back()->with('error', 'Bạn cần đăng nhập để theo dõi.');
        }

        // Check user bị ban
        if ($authUser->isBanned()) {
            return back()->with('error', 'Tài khoản của bạn hiện không thể theo dõi/hủy theo dõi.');
        }

        if ($authUser->id === $user->id) {
            return back()->with('error', 'Bạn không thể theo dõi chính mình.');
        }

        if (!$authUser->isFollowing($user)) {
            $authUser->follows()->attach($user->id);
            $user->notify(new FollowNotification($authUser));
        }

        return back()->with('success', "Đã theo dõi {$user->name}.");
    }

    public function unfollow(User $user)
    {
        $authUser = Auth::user();

        // Kiểm tra đăng nhập
        if (!$authUser) {
            return back()->with('error', 'Bạn cần đăng nhập để bỏ theo dõi.');
        }

        // Check user bị ban
        if ($authUser->isBanned()) {
            return back()->with('error', 'Tài khoản của bạn hiện không thể theo dõi/hủy theo dõi.');
        }

        if ($authUser->id === $user->id) {
            return back()->with('error', 'Bạn không thể bỏ theo dõi chính mình.');
        }

        $authUser->follows()->detach($user->id);

        return back()->with('success', "Đã bỏ theo dõi {$user->name}.");
    }

    public function getFollowers(User $user)
    {
        $followers = $user->followers()->get()->map(function ($follower) {
            return [
                'id' => $follower->id,
                'name' => $follower->name,
                'mention' => $follower->mention,
                'avatar' => $follower->avatar,
                'is_mutual' => Auth::check()
                    && Auth::user()->isFollowing($follower)
                    && $follower->isFollowing(Auth::user()),
            ];
        });

        return response()->json(['followers' => $followers]);
    }
}
