<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    protected $fillable = ['name', 'code', 'description'];

    public function floors()
    {
        return $this->hasMany(Floor::class);
    }
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
