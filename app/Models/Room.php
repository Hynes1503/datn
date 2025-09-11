<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Post;

class Room extends Model
{
    protected $fillable = ['floor_id', 'room_number', 'name', 'capacity', 'type', 'description'];

    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
