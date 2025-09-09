<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\Post;

class LikeNotification extends Notification
{
    use Queueable;

    protected $liker;
    protected $post;

    public function __construct(User $liker, Post $post)
    {
        $this->liker = $liker;
        $this->post = $post;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'liker_id' => $this->liker->id,
            'liker_name' => $this->liker->name,
            'liker_mention' => $this->liker->mention,
            'post_id' => $this->post->id,
            'post_slug' => $this->post->slug,
            'post_owner_mention' => $this->post->user->mention, // 👈 thêm dòng này
            'message' => "{$this->liker->name} đã thích bài viết của bạn: {$this->post->title}",
        ];
    }
}
