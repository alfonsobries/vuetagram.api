<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreatePostTest extends TestCase
{
    use WithFaker;

    /** @test */
    public function an_logged_user_can_add_a_post()
    {
        $user = factory(User::class)->create();
        $postData = $this->getPostData(['caption' => 'Hello Worlds :D']);

        $this
            ->actingAs($user)
            ->postJson(route('posts.store'), $postData)
            ->assertSuccessful();

        $this->assertEquals($postData['caption'], Post::latest()->first()->caption);
    }

    private function getPostData ($replace = []) {
        return array_merge([
            'photo' => UploadedFile::fake()->image('picture.jpg'),
            'caption' => $this->faker()->text,
        ], $replace);
    }
}
