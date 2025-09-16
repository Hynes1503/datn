<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'mention',
        'dob',
        'class',
        'major',
        'course',
        'student_id',
        'avatar',
        'email',
        'password',
        'role',
        'profile_visibility',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'dob' => 'date',
        'profile_visibility' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->mention)) {
                $user->mention = self::generateUniqueMention($user->name);
            }
        });
    }

    public function posts()
    {
        return $this->hasMany(Post::class)->orderBy('created_at', 'desc');
    }

    public function ownsPost(Post $post)
    {
        return $this->id === $post->user_id;
    }

    public function follows()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id')->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id')->withTimestamps();
    }

    public function isFollowing(User $user)
    {
        return $this->follows()->where('followed_id', $user->id)->exists();
    }

    public function getRouteKeyName()
    {
        return 'mention';
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar && Storage::disk('public')->exists($this->avatar)
            ? asset('storage/' . $this->avatar)
            : asset('images/default-avatar.png');
    }

    public function likes()
    {
        return $this->belongsToMany(Post::class, 'likes', 'user_id', 'post_id')->withTimestamps();
    }
}