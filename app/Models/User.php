<?php

namespace App\Models;

use App\Traits\HasManyPosts;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Overtrue\LaravelFollow\Traits\CanLike;
use Overtrue\LaravelFollow\Traits\CanFollow;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Overtrue\LaravelFollow\Traits\CanBeFollowed;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\User;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable,
        HasManyPosts,
        SoftDeletes,
        CanFollow,
        CanBeFollowed,
        CanLike;

    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';
    const ROLE_ROOT = 'root';

    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'website',
        'bio',
        'phone',
        'gender',
        'is_private',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_private' => 'boolean',
        'is_system' => 'boolean',
    ];

    /**
     * The attributes that should be threated as dates
     *
     * @var array
     */
    protected $dates = [
        'deleted_at',
    ];

    /**
     * @return int
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * If the user owns the model
     *
     * @param mixed $model
     * @return boolean
     */
    public function owns($model)
    {
        return $model->user_id === $this->id;
    }

    /**
     * If the user has a public profile
     *
     * @return void
     */
    public function getIsPublicAttribute()
    {
        return $this->is_private === false;
    }

    /**
     * Approve the user follower
     *
     * @param \App\Models\User $follower
     * @return void
     */
    public function approveFollower(User $follower)
    {
        $this->followers()->updateExistingPivot($follower->id, ['approved_at' => now()]);
    }
}
