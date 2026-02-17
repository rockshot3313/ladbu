<?php

namespace Ladbu\LaravelLadwireUserManagement\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

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
    public $users;

    public $name = '';
    public $email = '';
    public $role = 'user';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'role' => 'required|in:admin,user',
    ];

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function render()
    {
        $this->loadUsers();
        
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
        $query = User::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $query->orderBy($this->sortBy, $this->sortDirection);

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

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt('password'), // You may want to generate a random password or send an email
            'role' => $this->role,
        ]);

        $this->reset(['name', 'email', 'role', 'showCreateModal']);
        $this->loadUsers();
        $this->dispatch('user-created');
    }

    public function editUser($userId)
    {
        $this->selectedUserId = $userId;
        $user = User::find($userId);
        
        if ($user) {
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role = $user->role ?? 'user';
        }
        
        $this->showEditModal = true;
    }

    public function updateUser()
    {
        $this->validate([
            'email' => 'required|email|unique:users,email,' . $this->selectedUserId,
        ]);

        $user = User::find($this->selectedUserId);
        if ($user) {
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
            ]);
        }

        $this->reset(['selectedUserId', 'name', 'email', 'role', 'showEditModal']);
        $this->loadUsers();
        $this->dispatch('user-updated');
    }

    public function deleteUser($userId)
    {
        User::find($userId)?->delete();
        $this->loadUsers();
        $this->dispatch('user-deleted');
    }

    public function paginationView(): string
    {
        return 'livewire.pagination-links';
    }
}
