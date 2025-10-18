<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use App\Models\PostMedia;
use App\Notifications\LikeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Room;
use App\Models\Report;

class PostController extends Controller
{
    // public function index()
    // {
    //     $posts = Post::with('user')->latest();
    //     return view('user.posts.index', compact('posts'));
    // }

    public function home(Request $request)
    {
        $posts = Post::with(['user', 'media', 'room'])
            ->whereDoesntHave('reports', function ($query) {
                $query->where('status', 'resolved'); // hoặc 'banned' nếu bạn đặt vậy
            })
            ->latest()
            ->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'posts' => $posts->items(),
                'next_page_url' => $posts->nextPageUrl(),
            ]);
        }

        return view('home', compact('posts'));
    }

    public function topPosts()
    {
        $posts = Post::all()
            ->sortByDesc(fn($post) => $post->score)
            ->take(10);

        return view('user.posts.top', compact('posts'));
    }

    public function create()
    {
        $buildings = Building::orderBy('name')->get();
        return view('user.posts.create', compact('buildings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'media'   => 'nullable|array',
            'media.*' => 'file|mimes:jpg,jpeg,png,gif,webp,mp4,avi,mov,webm|max:51200',
            'hashtag' => 'nullable|string|max:255',
            'room_id' => [
                'nullable',
                'exists:rooms,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $room = Room::with('floor.building')->find($value);
                        if ($room && (!$room->floor || !$room->floor->building)) {
                            $fail('The selected room must belong to a valid floor and building.');
                        }
                    }
                },
            ],
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để đăng bài');
        }

        if ($user->isBanned()) {
            return back()->with('error', 'Tài khoản của bạn hiện không thể đăng bài.');
        }

        $slug = $this->generateRandomSlug(12);

        $hashtag = $request->hashtag ? str_replace('#', '', $request->hashtag) : null;
        $buildingId = $this->getBuildingIdFromRoom($request->room_id);

        try {
            $post = $user->posts()->create([
                'title'       => $request->title,
                'content'     => $request->content,
                'hashtag'     => $hashtag,
                'slug'        => $slug,
                'room_id'     => $request->room_id,
                'building_id' => $buildingId,
            ]);

            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $ext  = strtolower($file->getClientOriginalExtension());
                    $type = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']) ? 'image' : 'video';

                    $path = $file->store('posts/media', 'public');

                    $post->media()->create([
                        'media_path' => $path,
                        'media_type' => $type,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Post creation failed', [
                'user_id' => $user->id,
                'request' => $request->all(),
                'error'   => $e->getMessage(),
            ]);
            return back()->with('error', 'Tạo bài viết thất bại');
        }

        return redirect()->route('home')->with('success', 'Đăng bài thành công!');
    }

    private function generateRandomSlug($length = 12)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $slug = '';
        for ($i = 0; $i < $length; $i++) {
            $slug .= $characters[random_int(0, strlen($characters) - 1)];
        }

        while (Post::where('slug', $slug)->exists()) {
            $slug = $this->generateRandomSlug($length);
        }

        return $slug;
    }

    private function getBuildingIdFromRoom($roomId)
    {
        $buildingId = null;
        if ($roomId) {
            $room = Room::with('floor.building')->find($roomId);
            if ($room && $room->floor && $room->floor->building) {
                $buildingId = $room->floor->building->id;
            }
        }
        return $buildingId;
    }

    public function show(User $user, Post $post)
    {
        if ($post->user_id !== $user->id) {
            abort(404);
        }

        if (!Auth::check() || Auth::user()->role !== 'Admin') {
            $isBanned = Report::where('reportable_type', Post::class)
                ->where('reportable_id', $post->id)
                ->where('status', 'resolved')
                ->exists();

            if ($isBanned) {
                return redirect()->back()->with('error', 'Bài viết này đã bị khóa hoặc không tồn tại.');
            }
        }

        $sessionKey = 'viewed_post_' . $post->id;

        if ((!Auth::check() || Auth::id() !== $user->id) && !Session::has($sessionKey)) {
            $post->increment('views');
            Session::put($sessionKey, true);
        }

        $comments = $post->comments()
            ->with([
                'user',
                'replies' => function ($q) {
                    $q->with('user')->visible();
                }
            ])
            ->visible()
            ->get();

        return view('user.posts.show', compact('user', 'post', 'comments'));
    }

    public function edit(User $user, Post $post)
    {
        if ($post->user_id !== $user->id) {
            abort(404);
        }

        return view('user.posts.edit', compact('user', 'post'));
    }

    public function update(Request $request, User $user, Post $post)
    {
        if ($post->user_id !== $user->id) {
            abort(404);
        }

        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'hashtag' => 'nullable|string|max:255',
            'media'   => 'nullable|array',
            'media.*' => 'file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm|max:51200',
            'delete_media' => 'nullable|array',
            'delete_media.*' => 'integer|exists:post_media,id',
            'room_id' => [
                'nullable',
                'exists:rooms,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $room = Room::with('floor.building')->find($value);
                        if ($room && (!$room->floor || !$room->floor->building)) {
                            $fail('The selected room must belong to a valid floor and building.');
                        }
                    }
                },
            ],
        ]);

        $hashtag = $request->hashtag ? str_replace('#', '', $request->hashtag) : null;
        $buildingId = $this->getBuildingIdFromRoom($request->room_id);

        $post->update([
            'title'   => $request->title,
            'content' => $request->content,
            'hashtag' => $hashtag,
            'room_id' => $request->room_id,
            'building_id' => $buildingId,
        ]);

        if ($request->filled('delete_media')) {
            foreach ($request->delete_media as $mediaId) {
                $media = $post->media()->find($mediaId);
                if ($media) {
                    Storage::disk('public')->delete($media->media_path);
                    $media->delete();
                }
            }
        }

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('posts/media', 'public');
                $type = Str::startsWith($file->getMimeType(), 'video') ? 'video' : 'image';

                $post->media()->create([
                    'media_path' => $path,
                    'media_type' => $type,
                ]);
            }
        }

        return redirect()->route('posts.show', [
            'user' => $user->mention,
            'post' => $post->slug
        ])->with('success', 'Cập nhật bài viết thành công!');
    }

    public function destroy(User $user, Post $post)
    {
        if ($post->user_id !== $user->id) {
            abort(404);
        }

        foreach ($post->media as $media) {
            Storage::disk('public')->delete($media->media_path);
            $media->delete();
        }

        $post->delete();

        return redirect()->back()->with('success', 'Xóa bài viết thành công!');
    }

    public function toggleLike(Request $request, User $user, Post $post)
    {
        if ($post->user_id !== $user->id) {
            abort(404);
        }

        $authUser = Auth::user();
        if (!$authUser) {
            return response()->json(['error' => 'Bạn cần đăng nhập để thích bài viết.'], 403);
        }

        $wasLiked = $post->isLikedBy($authUser);
        if ($wasLiked) {
            $post->likes()->detach($authUser->id);
        } else {
            $post->likes()->attach($authUser->id);
            if ($authUser->id !== $post->user_id) {
                $post->user->notify(new LikeNotification($authUser, $post));
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'liked' => !$wasLiked,
                'likes_count' => $post->likes()->count(),
            ]);
        }

        return back()->with('success', $wasLiked ? 'Đã bỏ thích bài viết.' : 'Đã thích bài viết.');
    }

    public function byHashtag($hashtag)
    {

        $tags = array_unique(array_map('trim', explode(',', $hashtag)));

        $posts = Post::where(function ($query) use ($tags) {
            foreach ($tags as $tag) {
                $query->where('hashtag', 'like', '%' . $tag . '%');
            }
        })
            ->with(['user', 'media'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.posts.by_hashtag', [
            'posts' => $posts,
            'hashtag' => $hashtag,
        ]);
    }

    public function lost_index()
    {
        $posts = Post::where(function ($query) {
            $query->where('hashtag', 'LIKE', '%timdo%')
                ->orWhere('hashtag', 'LIKE', '%matdo%');
        })
            ->with(['user', 'media'])
            ->latest()
            ->paginate(10);

        return view('user.posts.lost_item', compact('posts'));
    }

    public function liked(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để xem các bài viết đã thích.');
        }

        $posts = $user->likes()
            ->with(['user', 'media', 'room'])
            ->whereDoesntHave('reports', function ($query) {
                $query->where('status', 'resolved');
            })
            ->latest()
            ->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'posts' => $posts->items(),
                'next_page_url' => $posts->nextPageUrl(),
            ]);
        }

        return view('user.posts.liked', compact('posts'));
    }
}
