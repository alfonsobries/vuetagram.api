<?php

use App\Models\Post;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Post::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(User::class)->create();
        },
        'caption' => $faker->optional()->text
    ];
});


$factory->state(Post::class, 'public_user', function ($faker) {
    return [
        'user_id' => function () {
            return factory(User::class)->state('public')->create();
        },
    ];
});

$factory->state(Post::class, 'private_user', function ($faker) {
    return [
        'user_id' => function () {
            return factory(User::class)->state('private')->create();
        },
    ];
});
