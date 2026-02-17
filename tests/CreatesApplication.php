<?php

namespace Ladbu\LaravelLadwireModule\Tests;

use Illuminate\Contracts\Auth\AccessResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\User;

trait CreatesApplication
{
    /**
     * Create a user and authenticate them.
     */
    protected function createUser(array $attributes = []): User
    {
        return User::factory()->create($attributes);
    }

    /**
     * Authenticate the user.
     */
    protected function authenticate(User $user): AccessResponse
    {
        $this->actingAs($user);
        
        return $this->get('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
    }

    /**
     * Sign out the user.
     */
    protected function signOut(): void
    {
        $this->post('/logout');
    }
}
