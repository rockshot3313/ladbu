<?php

namespace Ladbu\LaravelLadwireUserManagement\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortBy = 'name';
    public $sortDirection = 'asc';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $selectedUserId = null;

    public $name = '';
    public $email = '';
    public $role = 'user';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'role' => 'required|in:admin,user',
    ];

    public function render()
    {
        return view('laravel-ladwire-user-management::livewire.user-management', [
            'users' => $this->users,
        ]);
    }

    public function mount(): void
    {
        $this->loadUsers();
    }

    public function loadUsers(): void
    {
        // This is a mock implementation
        // In a real package, you would query the actual users table
        $query = collect([
            ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'role' => 'admin', 'created_at' => '2024-01-15'],
            ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'role' => 'user', 'created_at' => '2024-01-16'],
            ['id' => 3, 'name' => 'Bob Johnson', 'email' => 'bob@example.com', 'role' => 'user', 'created_at' => '2024-01-17'],
        ]);

        if ($this->search) {
            $query = $query->filter(function ($user) {
                return str_contains(strtolower($user['name']), strtolower($this->search)) ||
                       str_contains(strtolower($user['email']), strtolower($this->search));
            });
        }

        if ($this->sortBy) {
            $query = $query->sortBy($this->sortBy, $this->sortDirection);
        }

        $this->users = $query->paginate($this->perPage);
    }

    public function sort($column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
        
        $this->loadUsers();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function createUser()
    {
        $this->validate();

        // In a real implementation, you would create the user here
        $this->reset(['name', 'email', 'role', 'showCreateModal']);
        $this->dispatch('user-created');
    }

    public function editUser($userId)
    {
        $this->selectedUserId = $userId;
        // In a real implementation, you would load the user data here
        $this->showEditModal = true;
    }

    public function updateUser()
    {
        // In a real implementation, you would update the user here
        $this->reset(['selectedUserId', 'showEditModal']);
        $this->dispatch('user-updated');
    }

    public function deleteUser($userId)
    {
        // In a real implementation, you would delete the user here
        $this->dispatch('user-deleted');
    }

    public function paginationView(): string
    {
        return 'livewire.pagination-links';
    }
}
