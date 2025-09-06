<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'content',
        'hashtag',
        'likes',
        'shares',
        'views',
    ];

    protected $casts = [
        'likes' => 'integer',
        'shares' => 'integer',
        'views' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(PostImage::class);
    }
    public function getRouteKeyName()
    {
        return 'slug';
    }
    public function media()
    {
        return $this->hasMany(PostMedia::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id')->orderBy('created_at', 'desc');
    }
}
