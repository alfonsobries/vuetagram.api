<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostCrudTest extends TestCase
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

    /** @test */
    public function a_logged_user_can_update_his_own_post()
    {
        $post = factory(Post::class)->create(['caption' => 'Goodby world']);
        $user = $post->user;
        $postData = $this->getPostData(['caption' => 'Hello World :D']);

        $this->actingAs($user)
            ->putJson(route('posts.update', $post), $postData)
            ->assertSuccessful()
            ->assertJsonStructure(['id', 'caption']);

        $this->assertEquals($postData['caption'], $post->fresh()->caption);
    }

    /** @test */
    public function a_logged_user_can_delete_his_own_post()
    {
        $post = factory(Post::class)->create();
        $user = $post->user;

        $this->actingAs($user)
            ->deleteJson(route('posts.destroy', $post))
            ->assertSuccessful()
            ->assertJsonStructure(['id', 'caption']);

        $this->assertTrue($post->fresh()->trashed());
    }

    /** @test */
    public function a_random_user_cannot_delete_a_post()
    {
        $post = factory(Post::class)->create();
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->deleteJson(route('posts.destroy', $post))
            ->assertForbidden();
    }

    /** @test */
    public function a_guest_user_cannot_add_a_post()
    {
        $postData = $this->getPostData(['caption' => 'Hello Worlds :D']);

        $response = $this
            ->postJson(route('posts.store'), $postData)
            ->assertStatus(401);
    }

    /** @test */
    public function a_random_user_cannot_update_a_post()
    {
        $post = factory(Post::class)->create(['caption' => 'Goodby world']);
        $user = factory(User::class)->create();
        $postData = $this->getPostData(['caption' => 'Hello World :D']);

        $this->actingAs($user)
            ->putJson(route('posts.update', $post), $postData)
            ->assertForbidden();
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
