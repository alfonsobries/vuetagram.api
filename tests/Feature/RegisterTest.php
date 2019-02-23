<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    /** @test */
    public function can_register()
    {
        $userData = factory(User::class)->raw();
        $userData['password_confirmation'] = $userData['password'] = 'secret';
        
        $this->postJson(route('register'), $userData)
            ->assertSuccessful()
            ->assertJsonStructure(['id', 'name', 'email', 'username']);
    }

    /** @test */
    public function it_encrypts_password_of_a_registered_used()
    {
        $userData = factory(User::class)->raw();
        $userData['password_confirmation'] = $userData['password'] = 'secret';
        
        $this->postJson(route('register'), $userData);
        
        $this->assertTrue(Hash::check('secret', User::latest()->first()->password));
    }

    /** @test */
    public function it_store_all_the_fillable_user_data()
    {
        $userData = [
            'name' => 'Alfonso Bribiesca',
            'username' => 'alfonsobries',
            'email' => 'alfonso@vexilo.com',
            'website' => 'https://www.vexilo.com',
            'bio' => 'Full stack developer',
            'phone' => '+52 55 555555555',
            'gender' => User::GENDER_MALE,
            'is_private' => true,
        ];

        $this->postJson(route('register'), array_merge($userData, ['password' => 'secret', 'password_confirmation' => 'secret']));
        
        $this->assertDatabaseHas('users', $userData);
    }
}
