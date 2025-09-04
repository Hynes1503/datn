<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'dob',
        'class',
        'major',
        'course',
        'student_id',
        'avatar',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function ownsPost(Post $post)
    {
        return $this->id === $post->user_id;
    }
}