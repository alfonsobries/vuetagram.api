<?php

use Illuminate\Http\Request;


Route::get('posts', 'PostController@index')->name('posts.index');


Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', 'Auth\LoginController@logout')->name('logout');
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('account');
    Route::patch('settings/profile', 'Settings\ProfileController@update');
    Route::patch('settings/password', 'Settings\PasswordController@update');

    Route::post('posts', 'PostController@store')->name('posts.store');
    Route::put('posts/{post}', 'PostController@update')->name('posts.update');
    Route::delete('posts/{post}', 'PostController@destroy')->name('posts.destroy');
    
    Route::group(['prefix' => 'users/{user}'], function () {
        Route::post('follow', 'User\FollowController@follow')->name('users.follow');
        Route::post('unfollow', 'User\FollowController@unfollow')->name('users.unfollow');
    });
});

Route::group(['middleware' => 'guest:api'], function () {
    Route::post('login', 'Auth\LoginController@login')->name('login');
    Route::post('register', 'Auth\RegisterController@register')->name('register');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');
});
