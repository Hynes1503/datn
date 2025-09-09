<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Notifications\CommentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Lưu bình luận hoặc trả lời mới
     */
    public function store(Request $request, $userMention, Post $post)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Bạn cần đăng nhập để bình luận.'], 403);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $post->id,
            'content' => $request->content,
            'parent_id' => $request->parent_id,
        ]);

        $comment->load('user');

        // Gửi thông báo cho chủ bài viết (nếu không phải chính họ)
        if (Auth::id() !== $post->user_id) {
            $post->user->notify(new CommentNotification(Auth::user(), $post, $comment));
        }

        return response()->json([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                    'mention' => $comment->user->mention,
                    'avatar_url' => $comment->user->avatar_url,
                ],
                'post_id' => $post->id,
                'created_at' => $comment->created_at->toIso8601String(),
                'can_delete' => Auth::id() === $comment->user_id || Auth::user()->ownsPost($post),
            ],
        ], 201);
    }

    /**
     * Xóa bình luận
     */
    public function destroy($userMention, Post $post, Comment $comment)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Bạn cần đăng nhập để xóa bình luận.'], 403);
        }

        if (Auth::id() !== $comment->user_id && Auth::id() !== $post->user_id) {
            return response()->json(['error' => 'Bạn không có quyền xóa bình luận này.'], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bình luận đã được xóa.',
        ]);
    }
}