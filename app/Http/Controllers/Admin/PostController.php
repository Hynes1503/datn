<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use App\Models\Building;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
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
    private function generateRandomSlug($length = 12)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $slug = '';
        for ($i = 0; $i < $length; $i++) {
            $slug .= $characters[random_int(0, strlen($characters) - 1)];
        }

        // Đảm bảo slug là duy nhất
        while (Post::where('slug', $slug)->exists()) {
            $slug = $this->generateRandomSlug($length);
        }

        return $slug;
    }
    public function index(Request $request)
    {
        $query = Post::with(['user', 'building', 'room']);

        // Filter by user_id
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by hashtag
        if ($request->filled('hashtag')) {
            $query->where('hashtag', 'like', '%' . $request->hashtag . '%');
        }

        $posts = $query->latest()->paginate(10);
        $users = User::select('id', 'name')->get();

        return view('admin.posts.index', compact('posts', 'users'));
    }

    public function create()
    {
        $users = User::all();
        $buildings = Building::all();
        $rooms = Room::all();
        return view('admin.posts.create', compact('users', 'buildings', 'rooms'));
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

        // Kiểm tra user
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để đăng bài');
        }

        // Sinh slug ngẫu nhiên
        $slug = $this->generateRandomSlug(12);

        $hashtag = $request->hashtag ? str_replace('#', '', $request->hashtag) : null;
        $buildingId = $this->getBuildingIdFromRoom($request->room_id);

        $post = $user->posts()->create([
            'title'   => $request->title,
            'content' => $request->content,
            'hashtag' => $hashtag,
            'slug'    => $slug,
            'room_id' => $request->room_id,
            'building_id' => $buildingId,
        ]);

        if (!$post) {
            return back()->with('error', 'Tạo bài viết thất bại');
        }

        // Lưu media (ảnh + video)
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

        return redirect()->route('admin.posts.index')->with('success', 'Đăng bài thành công!');
    }

    public function show(Post $post)
    {
        $post->load('user', 'building', 'room', 'comments');
        return view('admin.posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        $users = User::all();
        $buildings = Building::all();
        $rooms = Room::all();
        return view('admin.posts.edit', compact('post', 'users', 'buildings', 'rooms'));
    }

    public function update(Request $request, Post $post)
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

        // Check authenticated user
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'You need to be logged in to update a post');
        }

        // Generate random slug
        $slug = $this->generateRandomSlug(12);

        // Process hashtag
        $hashtag = $request->hashtag ? str_replace('#', '', $request->hashtag) : null;
        $buildingId = $this->getBuildingIdFromRoom($request->room_id);

        // Update post
        $post->update([
            'user_id'     => $user->id,
            'title'       => $request->title,
            'content'     => $request->content,
            'hashtag'     => $hashtag,
            'slug'        => $slug,
            'room_id'     => $request->room_id,
            'building_id' => $buildingId,
        ]);

        // Handle media updates
        if ($request->hasFile('media')) {
            // Optionally delete existing media
            $post->media()->delete();
            foreach ($request->file('media') as $file) {
                $ext = strtolower($file->getClientOriginalExtension());
                $type = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']) ? 'image' : 'video';
                $path = $file->store('posts/media', 'public');
                $post->media()->create([
                    'media_path' => $path,
                    'media_type' => $type,
                ]);
            }
        }

        return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Bài viết đã bị xóa');
    }
}
