<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
        'content',
        'parent_id',
    ];

    protected $casts = [
        'parent_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }
    // App\Models\Comment.php
    public function scopeVisible($query)
    {
        return $query->whereDoesntHave('reports', function ($q) {
            $q->where('status', 'resolved');
        })
            ->whereDoesntHave('parent.reports', function ($q) {
                $q->where('status', 'resolved');
            });
    }
}
