<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    // Store a new comment or reply
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

        return response()->json([
            'success' => true,
            'comment' => $comment,
        ], 201);
    }

    // Delete a comment
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