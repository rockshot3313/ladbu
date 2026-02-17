<?php

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
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

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount(): void
    {
        // Component is ready, computed property will handle data
    }

    #[Computed]
    public function users()
    {
        $query = User::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate($this->perPage);
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
            'password' => bcrypt('password'),
            'role' => $this->role,
        ]);

        $this->reset(['name', 'email', 'role', 'showCreateModal']);
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
        $this->dispatch('user-updated');
    }

    public function deleteUser($userId)
    {
        User::find($userId)?->delete();
        $this->dispatch('user-deleted');
    }

    public function paginationView(): string
    {
        return 'livewire.pagination-links';
    }
}; ?>

<flux:heading>User Management</flux:heading>

<div class="flex justify-between items-center mb-6">
    <div></div>
    <flux:button wire:click="$toggle('showCreateModal')">
        <flux:icon.plus class="w-4 h-4 mr-2" />
        Add New User
    </flux:button>
</div>

<!-- Search and Filter -->
<flux:card class="mb-6">
    <flux:card.content>
        <flux:grid>
            <flux:textbox 
                wire:model.live="search" 
                placeholder="Search users..." 
                label="Search"
            />
            <flux:select wire:model.live="perPage" label="Per Page">
                <flux:select.option value="10">10 per page</flux:select.option>
                <flux:select.option value="25">25 per page</flux:select.option>
                <flux:select.option value="50">50 per page</flux:select.option>
            </flux:select>
        </flux:grid>
    </flux:card.content>
</flux:card>

<!-- Users Table -->
<flux:table>
    <flux:table.header>
        <flux:table.row>
            <flux:table.heading>
                <flux:button variant="ghost" size="sm" wire:click="sort('name')">
                    Name
                    @if($sortBy === 'name')
                        <flux:icon.caret-{{ $sortDirection === 'asc' ? 'up' : 'down' }} class="w-4 h-4" />
                    @endif
                </flux:button>
            </flux:table.heading>
            <flux:table.heading>
                <flux:button variant="ghost" size="sm" wire:click="sort('email')">
                    Email
                    @if($sortBy === 'email')
                        <flux:icon.caret-{{ $sortDirection === 'asc' ? 'up' : 'down' }} class="w-4 h-4" />
                    @endif
                </flux:button>
            </flux:table.heading>
            <flux:table.heading>Role</flux:table.heading>
            <flux:table.heading>
                <flux:button variant="ghost" size="sm" wire:click="sort('created_at')">
                    Created
                    @if($sortBy === 'created_at')
                        <flux:icon.caret-{{ $sortDirection === 'asc' ? 'up' : 'down' }} class="w-4 h-4" />
                    @endif
                </flux:button>
            </flux:table.heading>
            <flux:table.heading>Actions</flux:table.heading>
        </flux:table.row>
    </flux:table.header>
    
    <flux:table.body>
        @forelse($this->users as $user)
            <flux:table.row>
                <flux:table.cell>
                    <div class="flex items-center">
                        <flux:avatar>{{ substr($user->name, 0, 1) }}</flux:avatar>
                        <div class="ml-4">
                            <flux:text weight="medium">{{ $user->name }}</flux:text>
                        </div>
                    </div>
                </flux:table.cell>
                <flux:table.cell>
                    <flux:text>{{ $user->email }}</flux:text>
                </flux:table.cell>
                <flux:table.cell>
                    <flux:badge variant="{{ $user->role === 'admin' ? 'primary' : 'success' }}">
                        {{ $user->role ?? 'user' }}
                    </flux:badge>
                </flux:table.cell>
                <flux:table.cell>
                    <flux:text size="sm" color="gray">{{ $user->created_at->format('Y-m-d') }}</flux:text>
                </flux:table.cell>
                <flux:table.cell>
                    <flux:button variant="ghost" size="sm" wire:click="editUser({{ $user->id }})">
                        <flux:icon.pencil class="w-4 h-4" />
                    </flux:button>
                    <flux:button variant="ghost" size="sm" wire:click="deleteUser({{ $user->id }})" class="text-red-600">
                        <flux:icon.trash class="w-4 h-4" />
                    </flux:button>
                </flux:table.cell>
            </flux:table.row>
        @empty
            <flux:table.row>
                <flux:table.cell colspan="5" class="text-center">
                    <flux:text color="gray">No users found</flux:text>
                </flux:table.cell>
            </flux:table.row>
        @endforelse
    </flux:table.body>
</flux:table>

<!-- Pagination -->
@if($this->users->hasPages())
    <div class="mt-4">
        {{ $this->users->links() }}
    </div>
@endif

<!-- Create User Modal -->
@if($showCreateModal)
    <flux:modal wire:model="showCreateModal">
        <flux:modal.heading>Create New User</flux:modal.heading>
        
        <flux:modal.content>
            <flux:form wire:submit="createUser">
                <flux:textbox 
                    wire:model="name" 
                    label="Name" 
                    required
                />
                @error('name') <flux:error>{{ $message }}</flux:error> @enderror
                
                <flux:textbox 
                    wire:model="email" 
                    type="email" 
                    label="Email" 
                    required
                />
                @error('email') <flux:error>{{ $message }}</flux:error> @enderror
                
                <flux:select wire:model="role" label="Role" required>
                    <flux:select.option value="user">User</flux:select.option>
                    <flux:select.option value="admin">Admin</flux:select.option>
                </flux:select>
                @error('role') <flux:error>{{ $message }}</flux:error> @enderror
                
                <div class="flex justify-end space-x-3 mt-6">
                    <flux:button variant="ghost" wire:click="$toggle('showCreateModal')">
                        Cancel
                    </flux:button>
                    <flux:button type="submit">
                        Create User
                    </flux:button>
                </div>
            </flux:form>
        </flux:modal.content>
    </flux:modal>
@endif

<!-- Edit User Modal -->
@if($showEditModal)
    <flux:modal wire:model="showEditModal">
        <flux:modal.heading>Edit User</flux:modal.heading>
        
        <flux:modal.content>
            <flux:form wire:submit="updateUser">
                <flux:textbox 
                    wire:model="name" 
                    label="Name" 
                    required
                />
                
                <flux:textbox 
                    wire:model="email" 
                    type="email" 
                    label="Email" 
                    required
                />
                
                <flux:select wire:model="role" label="Role" required>
                    <flux:select.option value="user">User</flux:select.option>
                    <flux:select.option value="admin">Admin</flux:select.option>
                </flux:select>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <flux:button variant="ghost" wire:click="$toggle('showEditModal')">
                        Cancel
                    </flux:button>
                    <flux:button type="submit">
                        Update User
                    </flux:button>
                </div>
            </flux:form>
        </flux:modal.content>
    </flux:modal>
@endif
