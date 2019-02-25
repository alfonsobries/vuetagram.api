<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FollowController extends Controller
{
    public function follow(User $user, Request $request)
    {
        $result = auth()->user()->follow($user);
        
        if ($user->is_public) {
            $user->approveFollower(auth()->user());
        }
        
        return [
            'follower' => auth()->user(),
            'followable' => $user,
        ];
    }

    public function unfollow(User $user, Request $request)
    {
        auth()->user()->unfollow($user);
        
        return [
            'follower' => auth()->user(),
            'followable' => $user,
        ];
    }
}
