<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $query = Comment::with(['user', 'post']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('post_id')) {
            $query->where('post_id', $request->post_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where('content', 'like', '%' . $request->search . '%');
        }

        $comments = $query->latest()->paginate(20);

        $users = User::select('id', 'name')->get();
        $posts = Post::select('id', 'title')->get();

        return view('admin.comments.index', compact('comments', 'users', 'posts'));
    }

    public function show(Comment $comment)
    {
        $comment->load(['user', 'post']);
        return view('admin.comments.show', compact('comment'));
    }

    public function edit(Comment $comment)
    {
        return view('admin.comments.edit', compact('comment'));
    }

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

    public function destroy(Request $request, Comment $comment)
    {
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