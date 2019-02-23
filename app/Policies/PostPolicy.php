<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can publish a post
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function store(User $user)
    {
        // Every logged user can publish a post
        return true;
    }

    /**
     * Determine if the user can update a post
     *
     * @param \App\Models\User $user
     * @param \App\Models\Post $post
     * @return void
     */
    public function update(User $user, Post $post)
    {
        return $user->owns($post);
    }

    /**
     * Determine if the user can delete a post
     *
     * @param \App\Models\User $user
     * @param \App\Models\Post $post
     * @return void
     */
    public function destroy(User $user, Post $post)
    {
        return $user->owns($post);
    }
}
