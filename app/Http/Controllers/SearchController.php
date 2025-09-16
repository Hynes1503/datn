<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        $users = collect();
        $posts = collect();

        if ($query) {
            // Field luôn hiển thị
            $users = User::where('name', 'like', "%{$query}%")
                ->orWhere(function ($q) use ($query) {
                    $optionalFields = [
                        'mention',
                        'dob',
                        'class',
                        'major',
                        'course',
                        'student_id',
                    ];

                    foreach ($optionalFields as $field) {
                        $q->orWhere(function ($sub) use ($field, $query) {
                            $sub->where($field, 'like', "%{$query}%")
                                ->whereRaw("JSON_EXTRACT(profile_visibility, '$.\"$field\"') IN ('1', true)");
                        });
                    }
                })
                ->take(10)
                ->get();

            // Tìm kiếm bài viết
            $posts = Post::where('title', 'like', "%{$query}%")
                ->orWhere('content', 'like', "%{$query}%")
                ->orWhere('hashtag', 'like', "%{$query}%")
                ->latest()
                ->take(10)
                ->get();
        }

        return view('user.search', compact('query', 'users', 'posts'));
    }


    public function suggestions(Request $request)
    {
        $query = $request->get('q', '');
        $user = auth()->user();

        $users = User::query()
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                    ->orWhere('mention', 'like', "%$query%");
            })
            ->when($user, function ($q) use ($user) {
                // gợi ý thêm theo cùng khóa, lớp, ngành
                $q->orWhere('class', $user->class)
                    ->orWhere('course', $user->course)
                    ->orWhere('major', $user->major);
            })
            ->limit(5)
            ->get();

        return response()->json($users->map(function ($u) {
            return [
                'name' => $u->name,
                'mention' => $u->mention,
                'class' => $u->class,
                'course' => $u->course,
                'major' => $u->major,
                'avatar' => $u->avatar_url,
                'url' => route('profile.show', $u->mention), // route profile user
            ];
        }));
    }
}
