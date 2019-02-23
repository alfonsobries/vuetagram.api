<?php

namespace App\Traits;

use App\Models\Post;

trait HasManyPosts
{
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
