<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Post extends Model implements HasMedia
{
    use HasMediaTrait,
        BelongsToUser,
        SoftDeletes;

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
}
