<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use App\Models\PostImage;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user')->latest()->paginate(10);
        return view('user.posts.index', compact('posts'));
    }
    
    public function home()
    {
        $posts = Post::with('user')->latest()->paginate(10);
        return view('home', compact('posts'));
    }

    /**
     * Form tạo bài viết
     */
    public function create()
    {
        return view('user.posts.create');
    }

    /**
     * Lưu bài viết mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'content'  => 'required|string',
            'images'   => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,gif,webp',
            'hashtag'  => 'nullable|string|max:255',
        ]);

        // Tạo post qua quan hệ user
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để đăng bài');
        }

        $post = $user->posts()->create([
            'title'   => $request->title,
            'content' => $request->content,
            'hashtag' => $request->hashtag,
        ]);

        // Nếu $post rỗng -> log để debug
        if (! $post) {
            Log::error('Post not created', ['user_id' => Auth::id(), 'request' => $request->all()]);
            return back()->with('error', 'Tạo bài viết thất bại');
        }

        // Lưu nhiều ảnh (nếu có)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('posts', 'public'); // Lưu vào storage/app/public/posts
                $post->images()->create(['image_path' => $path]);
            }
        }

        return redirect()->route('posts.index')->with('success', 'Đăng bài thành công!');
    }

    /**
     * Hiển thị chi tiết bài viết
     */
    public function show(Post $post)
    {
        $post->increment('views'); // Tăng lượt xem
        return view('user.posts.show', compact('post'));
    }

    /**
     * Form chỉnh sửa bài viết
     */
    public function edit(Post $post)
    {
        return view('user.posts.edit', compact('post'));
    }

    /**
     * Cập nhật bài viết
     */
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'content'  => 'required|string',
            'images'   => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,gif,webp',
            'hashtag'  => 'nullable|string|max:255',
        ]);

        // Cập nhật thông tin bài viết
        $post->update([
            'title'   => $request->title,
            'content' => $request->content,
            'hashtag' => $request->hashtag,
        ]);

        // Xử lý ảnh: xóa toàn bộ ảnh cũ và thêm ảnh mới nếu có
        if ($request->hasFile('images')) {
            // Xóa toàn bộ ảnh cũ
            foreach ($post->images as $image) {
                Storage::disk('public')->delete($image->image_path); // Xóa file ảnh khỏi storage
                $image->delete(); // Xóa bản ghi trong database
            }

            // Thêm ảnh mới
            foreach ($request->file('images') as $image) {
                $path = $image->store('posts', 'public'); // Lưu vào storage/app/public/posts
                $post->images()->create(['image_path' => $path]);
            }
        }

        return redirect()->route('posts.index')->with('success', 'Cập nhật bài viết thành công!');
    }

    /**
     * Xoá bài viết
     */
    public function destroy(Post $post)
    {
        // Xóa ảnh liên quan trước khi xóa bài viết
        foreach ($post->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }
        $post->delete();
        return redirect()->route('posts.index')->with('success', 'Xoá bài viết thành công!');
    }
}