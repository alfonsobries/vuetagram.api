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
}
