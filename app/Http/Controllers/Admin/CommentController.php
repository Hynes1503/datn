<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Danh sách bình luận (dùng trong admin)
     */
    public function index(Request $request)
    {
        $query = Comment::with(['user', 'post']);

        // Filter by user_id
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by post_id
        if ($request->filled('post_id')) {
            $query->where('post_id', $request->post_id);
        }

        // Filter by creation date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Existing search filter for content
        if ($request->filled('search')) {
            $query->where('content', 'like', '%' . $request->search . '%');
        }

        $comments = $query->latest()->paginate(20);

        // Fetch users and posts for filter dropdowns
        $users = User::select('id', 'name')->get();
        $posts = Post::select('id', 'title')->get();

        return view('admin.comments.index', compact('comments', 'users', 'posts'));
    }

    /**
     * Hiển thị 1 comment chi tiết
     */
    public function show(Comment $comment)
    {
        $comment->load(['user', 'post']);
        return view('admin.comments.show', compact('comment'));
    }

    /**
     * Form sửa comment
     */
    public function edit(Comment $comment)
    {
        return view('admin.comments.edit', compact('comment'));
    }

    /**
     * Cập nhật nội dung comment
     */
    public function update(Request $request, Comment $comment)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment->update([
            'content' => $request->input('content'),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'comment' => $comment->fresh(['user', 'post']),
            ]);
        }

        return redirect()->route('admin.comments.index')
            ->with('success', 'Cập nhật bình luận thành công.');
    }

    /**
     * Xóa comment (và cả replies của nó nếu có)
     */
    public function destroy(Request $request, Comment $comment)
    {
        // Xóa replies trước (nếu muốn cứng rắn)
        $comment->replies()->delete();
        $comment->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Bình luận đã được xóa.',
            ]);
        }

        return redirect()->route('admin.comments.index')
            ->with('success', 'Xóa bình luận thành công.');
    }
}