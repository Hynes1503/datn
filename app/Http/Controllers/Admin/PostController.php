<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use App\Models\Building;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user', 'building', 'room')->latest()->paginate(10);
        return view('admin.posts.index', compact('posts'));
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
            'user_id'    => 'required|exists:users,id',
            'title'      => 'required|string|max:255',
            'content'    => 'required|string',
            'building_id'=> 'nullable|exists:buildings,id',
            'room_id'    => 'nullable|exists:rooms,id',
            'hashtag'    => 'nullable|string|max:255',
        ]);

        $slug = Str::slug($request->title);
        if (Post::where('slug', $slug)->exists()) {
            $slug .= '-' . time();
        }

        Post::create([
            'user_id'     => $request->user_id,
            'title'       => $request->title,
            'slug'        => $slug,
            'content'     => $request->content,
            'hashtag'     => $request->hashtag,
            'building_id' => $request->building_id,
            'room_id'     => $request->room_id,
        ]);

        return redirect()->route('admin.posts.index')->with('success', 'Bài viết đã được tạo');
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
            'user_id'    => 'required|exists:users,id',
            'title'      => 'required|string|max:255',
            'content'    => 'required|string',
            'building_id'=> 'nullable|exists:buildings,id',
            'room_id'    => 'nullable|exists:rooms,id',
            'hashtag'    => 'nullable|string|max:255',
        ]);

        $slug = Str::slug($request->title);
        if (Post::where('slug', $slug)->where('id', '!=', $post->id)->exists()) {
            $slug .= '-' . time();
        }

        $post->update([
            'user_id'     => $request->user_id,
            'title'       => $request->title,
            'slug'        => $slug,
            'content'     => $request->content,
            'hashtag'     => $request->hashtag,
            'building_id' => $request->building_id,
            'room_id'     => $request->room_id,
        ]);

        return redirect()->route('admin.posts.index')->with('success', 'Bài viết đã được cập nhật');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Bài viết đã bị xóa');
    }
}
