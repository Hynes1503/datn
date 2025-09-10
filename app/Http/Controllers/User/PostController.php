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
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'media'   => 'nullable|array',
            'media.*' => 'file|mimes:jpg,jpeg,png,gif,webp,mp4,avi,mov,webm|max:51200',
            'hashtag' => 'nullable|string|max:255',
        ]);

        // Kiểm tra user
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để đăng bài');
        }

        // Sinh slug ngẫu nhiên
        $slug = $this->generateRandomSlug(12);

        $hashtag = $request->hashtag ? str_replace('#', '', $request->hashtag) : null;

        $post = $user->posts()->create([
            'title'   => $request->title,
            'content' => $request->content,
            'hashtag' => $hashtag,
            'slug'    => $slug,
        ]);


        if (!$post) {
            Log::error('Post not created', ['user_id' => $user->id, 'request' => $request->all()]);
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

        return redirect()->route('home')->with('success', 'Đăng bài thành công!');
    }

    /**
     * Sinh slug ngẫu nhiên với độ dài $length, chỉ chứa chữ hoa và thường
     */
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

    /**
     * Hiển thị chi tiết bài viết
     */
    public function show(User $user, Post $post)
    {
        // Đảm bảo bài viết thuộc user
        if ($post->user_id !== $user->id) {
            abort(404);
        }

        // Key để check trong session
        $sessionKey = 'viewed_post_' . $post->id;

        // Nếu chưa xem và không phải chính chủ thì mới tăng view
        if ((!Auth::check() || Auth::id() !== $user->id) && !Session::has($sessionKey)) {
            $post->increment('views');
            Session::put($sessionKey, true);
        }

        return view('user.posts.show', compact('user', 'post'));
    }

    /**
     * Form chỉnh sửa bài viết
     */
    public function edit(User $user, Post $post)
    {
        // Đảm bảo bài viết thuộc user
        if ($post->user_id !== $user->id) {
            abort(404);
        }

        return view('user.posts.edit', compact('user', 'post'));
    }

    /**
     * Cập nhật bài viết
     */
    public function update(Request $request, User $user, Post $post)
    {
        // Đảm bảo bài viết thuộc user
        if ($post->user_id !== $user->id) {
            abort(404);
        }

        // Validate
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'hashtag' => 'nullable|string|max:255',
            'media'   => 'nullable|array',
            'media.*' => 'file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm|max:51200', // 50MB
            'delete_media' => 'nullable|array',
            'delete_media.*' => 'integer|exists:post_media,id',
        ]);

        // Cập nhật thông tin bài viết
        // Loại bỏ toàn bộ dấu #
        $hashtag = $request->hashtag ? str_replace('#', '', $request->hashtag) : null;

        $post->update([
            'title'   => $request->title,
            'content' => $request->content,
            'hashtag' => $hashtag,
        ]);


        // Xử lý xóa media được chọn
        if ($request->filled('delete_media')) {
            foreach ($request->delete_media as $mediaId) {
                $media = $post->media()->find($mediaId);
                if ($media) {
                    // Xóa file trong storage
                    Storage::disk('public')->delete($media->media_path);
                    // Xóa record trong DB
                    $media->delete();
                }
            }
        }

        // Xử lý upload media mới
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

    /**
     * Xóa bài viết
     */
    public function destroy(User $user, Post $post)
    {
        // Đảm bảo bài viết thuộc user
        if ($post->user_id !== $user->id) {
            abort(404);
        }

        // Xóa media liên quan trước khi xóa bài viết
        foreach ($post->media as $media) {
            Storage::disk('public')->delete($media->media_path);
            $media->delete();
        }

        // Xóa bài viết
        $post->delete();

        return redirect()->back()->with('success', 'Xóa bài viết thành công!');
    }

    /**
     * Toggle like/unlike bài viết
     */
    public function toggleLike(Request $request, User $user, Post $post)
    {
        // Đảm bảo bài viết thuộc user
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
            // Gửi thông báo cho chủ bài viết (nếu không phải chính họ)
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
        // Tách và chuẩn hóa danh sách hashtag, loại bỏ trùng lặp
        $tags = array_unique(array_map('trim', explode(',', $hashtag)));

        // Tìm bài post chứa tất cả hashtag
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
}
