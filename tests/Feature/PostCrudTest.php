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

    /** @test */
    public function a_guest_user_list_latest_public_users_posts()
    {
        $postForPublicUsers = factory(Post::class, 5)->state('public_user')->create();
        $postForPrivateUsers = factory(Post::class, 5)->state('private_user')->create();

        $response = $this->getJson(route('posts.index'))->assertSuccessful();

        $pagination = $response->json();

        $this->assertEquals(5, $pagination['total']);
        $this->assertEmpty(collect($pagination['data'])->pluck('id')->diff($postForPublicUsers->pluck('id')));
    }

    /** @test */
    public function a_loggued_user_list_the_latest_posts_of_the_user_it_follow_and_his_own_posts()
    {
        $user = factory(User::class)->create();

        factory(Post::class, 3)->create();
        
        // Post that should apper
        $userPosts = factory(Post::class, 2)->create(['user_id' => $user->id]);
        $postsToCheck = factory(Post::class, 3)->create();
        $postsFromUserNotApproved = factory(Post::class, 2)->create();
        $userPosts2 = factory(Post::class, 1)->create(['user_id' => $user->id]);
        
        $postsToCheck->pluck('user')->each->approveFollower($user);

        $postsFromUserNotApproved->pluck('user')->each->follow($user);
        
        $allPostsIds = $userPosts->pluck('id')->merge($postsToCheck->pluck('id'))->merge($userPosts2->pluck('id'));

        
        $response = $this
        ->actingAs($user)
        ->getJson(route('posts.index'))
        ->assertSuccessful();
        
        $pagination = $response->json();
        
        $this->assertEquals(6, $pagination['total']);
        $this->assertEmpty(collect($pagination['data'])->pluck('id')->diff($allPostsIds));
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
