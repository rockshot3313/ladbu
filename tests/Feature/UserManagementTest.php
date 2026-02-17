<?php

namespace Ladbu\LaravelLadwireModule\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Ladbu\LaravelLadwireModule\Http\Livewire\UserManagement;
use Livewire\Livewire;
use App\Models\User;

class UserManagementTest extends RefreshDatabase
{
    /** @test */
    public function user_management_component_renders_successfully()
    {
        Livewire::test('user-management')
            ->assertSee('User Management')
            ->assertSee('Add New User')
            ->assertSee('Search users...')
            ->assertSee('Per Page');
    }

    /** @test */
    public function user_management_displays_user_list()
    {
        Livewire::test('user-management')
            ->assertViewIs('laravel-ladwire-user-management::livewire.user-management')
            ->assertViewHas('users');
    }

    /** @test */
    public function user_management_can_search_users()
    {
        Livewire::test('user-management')
            ->set('search', 'John')
            ->call('search')
            ->assertSee('John Doe');
    }

    /** @test */
    public function user_management_can_create_user()
    {
        Livewire::test('user-management')
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('role', 'user')
            ->call('createUser')
            ->assertDispatched('user-created');
    }

    /** @test */
    public function user_management_can_edit_user()
    {
        Livewire::test('user-management')
            ->set('selectedUserId', 1)
            ->call('editUser', 1)
            ->assertDispatched('user-updated');
    }

    /** @test */
    public function user_management_can_delete_user()
    {
        Livewire::test('user-management')
            ->call('deleteUser', 1)
            ->assertDispatched('user-deleted');
    }

    /** @test */
    public function user_management_validates_required_fields()
    {
        Livewire::test('user-management')
            ->set('name', '')
            ->call('createUser')
            ->assertHasErrors(['name' => 'The name field is required.']);
    }
}
