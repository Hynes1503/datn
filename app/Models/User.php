<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

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
        // Tự sinh mention khi tạo user mới
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

    // public static function generateUniqueMention($name): string
    // {
    //     $base = Str::slug($name, '');
    //     $mention = $base;
    //     $counter = 1;

    //     while (self::where('mention', $mention)->exists()) {
    //         $mention = $base . $counter;
    //         $counter++;
    //     }

    //     return $mention;
    // }

    public function getRouteKeyName()
    {
        return 'mention';
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar && \Storage::disk('public')->exists($this->avatar)
            ? asset('storage/'.$this->avatar)
            : asset('images/default-avatar.png');
    }
}
