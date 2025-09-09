<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;

class CommentNotification extends Notification
{
    use Queueable;

    protected $commenter;
    protected $post;
    protected $comment;

    public function __construct(User $commenter, Post $post, Comment $comment)
    {
        $this->commenter = $commenter;
        $this->post = $post;
        $this->comment = $comment;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'commenter_id' => $this->commenter->id,
            'commenter_name' => $this->commenter->name,
            'commenter_mention' => $this->commenter->mention,
            'post_id' => $this->post->id,
            'post_slug' => $this->post->slug,
            'post_owner_mention' => $this->post->user->mention, // 👈 bắt buộc
            'comment_id' => $this->comment->id,
            'message' => "{$this->commenter->name} đã bình luận bài viết của bạn: {$this->post->title}",
        ];
    }
}
