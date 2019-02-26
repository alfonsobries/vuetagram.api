<?php

namespace App\Models;

use App\Models\User;
use App\Traits\BelongsToUser;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Overtrue\LaravelFollow\Traits\CanBeLiked;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Post extends Model implements HasMedia
{
    use HasMediaTrait,
        BelongsToUser,
        SoftDeletes,
        CanBeLiked;

    protected $fillable = [
        'caption',
        'latitude',
        'longitude',
    ];

    /**
     * Register the spatie media library media for this model.
     */
    public function registerMediaCollections()
    {
        $this
            ->addMediaCollection('photo')
            ->singleFile();
    }

    /**
     * Stores the photo in the media library
     *
     * @param \Illuminate\Http\UploadedFile $photo
     * @return void
     */
    public function setPhotoAttribute(UploadedFile $photo)
    {
        $this->addMedia($photo)
            ->toMediaCollection('photo');
    }

    /**
     * Posts that belongs to a public users
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopePublic($query)
    {
        return $query->whereHas('user', function ($query) {
            return $query->public();
        });
    }

    /**
     * Posts that should be listed for the user used a param
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param \App\Models\User $user
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeFor($query, User $user)
    {
        return $query->where(function ($query) use ($user) {
            // Post from the user his follow (and its approved)
            $query->whereHas('user', function ($query) use ($user) {
                return $query->whereHas('followers', function ($query) use ($user) {
                    return $query->where('user_id', $user->id)->whereNotNull('approved_at');
                });
            });
        // Or his own posts
        })->orWhere('user_id', $user->id);
    }
}
