<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserFollowTest extends TestCase
{
    /** @test */
    public function an_user_can_follow_another_public_user_and_is_inmediatly_approved()
    {
        $follower = factory(User::class)->create();
        $followable = factory(User::class)->state('public')->create();

        $this->actingAs($follower)
            ->postJson(route('users.follow', $followable))
            ->assertSuccessful();

        $this->assertTrue($followable->isFollowedBy($follower));
        $this->assertTrue($followable->followers()->where('id', $follower->id)->whereNotNull('followables.approved_at')->exists());
    }

    /** @test */
    public function an_user_can_follow_another_private_user_but_is_not_inmediatly_approved()
    {
        $follower = factory(User::class)->create();
        $followable = factory(User::class)->state('private')->create();

        $this->actingAs($follower)
            ->postJson(route('users.follow', $followable))
            ->assertSuccessful();

        $this->assertTrue($followable->isFollowedBy($follower));
        $this->assertTrue($followable->followers()->where('id', $follower->id)->whereNull('followables.approved_at')->exists());
    }

    /** @test */
    public function an_user_can_unfollow_an_user()
    {
        $follower = factory(User::class)->create();
        $followable = factory(User::class)->state('public')->create();

        $follower->follow($followable);

        $this->actingAs($follower)
            ->postJson(route('users.unfollow', $followable))
            ->assertSuccessful();

        
        $this->assertFalse($followable->isFollowedBy($follower));
    }
}
