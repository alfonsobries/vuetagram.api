<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreatePostTest extends TestCase
{
    use WithFaker;

    /** @test */
    public function a_logged_user_can_add_a_post()
    {
        $user = factory(User::class)->create();
        $postData = $this->getPostData(['caption' => 'Hello Worlds :D']);

        $response = $this
            ->actingAs($user)
            ->postJson(route('posts.store'), $postData)
            ->assertSuccessful()
            ->assertJsonStructure(['id', 'caption']);

        $post = Post::find($response->json()['id']);
        
        $this->assertEquals($postData['caption'], $post->caption);

    
        $this->assertNotNull($post->getFirstMedia('photo'));
    }

    private function getPostData($replace = [])
    {
        Storage::fake('public');

        return array_merge([
            'photo' => UploadedFile::fake()->image('picture.jpg'),
            'caption' => $this->faker()->text,
        ], $replace);
    }
}
