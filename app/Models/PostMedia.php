<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostMedia extends Model
{
    use HasFactory;
    protected $fillable = ['post_id', 'media_path', 'media_type'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

}
