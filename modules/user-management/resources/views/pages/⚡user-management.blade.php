<?php

use Livewire\Component;

new class extends Component {
    public $users = [];
    public $search = '';
    public $perPage = 10;

    public function mount(): void
    {
        $this->loadUsers();
    }

    public function loadUsers(): void
    {
        // Mock data - replace with actual database query
        $this->users = [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'role' => 'Admin', 'created_at' => '2024-01-15'],
            ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'role' => 'User', 'created_at' => '2024-01-20'],
            ['id' => 3, 'name' => 'Bob Johnson', 'email' => 'bob@example.com', 'role' => 'User', 'created_at' => '2024-02-01'],
        ];
    }

    public function deleteUser($userId): void
    {
        // Implement user deletion logic
        $this->loadUsers();
    }
};
?>

<flux:heading>User Management</flux:heading>

<!-- Search and Actions -->
<flux:card class="mb-6">
    <flux:card.content>
        <div class="flex items-center justify-between">
            <flux:input 
                wire:model.live="search" 
                placeholder="Search users..." 
                class="max-w-xs"
            />
            
            <flux:button variant="primary">
                <flux:icon.plus class="w-4 h-4 mr-2" />
                Add User
            </flux:button>
        </div>
    </flux:card.content>
</flux:card>

<!-- Users Table -->
<flux:card>
    <flux:card.header>
        <flux:card.title>Users</flux:card.title>
        <flux:card.description>Manage application users</flux:card.description>
    </flux:card.header>
    
    <flux:card.content>
        <flux:table>
            <flux:table.header>
                <flux:table.row>
                    <flux:table.cell>Name</flux:table.cell>
                    <flux:table.cell>Email</flux:table.cell>
                    <flux:table.cell>Role</flux:table.cell>
                    <flux:table.cell>Created</flux:table.cell>
                    <flux:table.cell>Actions</flux:table.cell>
                </flux:table.row>
            </flux:table.header>
            
            <flux:table.body>
                @foreach($users as $user)
                    <flux:table.row>
                        <flux:table.cell>{{ $user['name'] }}</flux:table.cell>
                        <flux:table.cell>{{ $user['email'] }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge variant="{{ $user['role'] === 'Admin' ? 'primary' : 'secondary' }}">
                                {{ $user['role'] }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>{{ $user['created_at'] }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex space-x-2">
                                <flux:button size="sm" variant="outline">
                                    <flux:icon.edit class="w-4 h-4" />
                                </flux:button>
                                <flux:button 
                                    size="sm" 
                                    variant="outline" 
                                    wire:click="deleteUser({{ $user['id'] }})"
                                >
                                    <flux:icon.trash class="w-4 h-4" />
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.body>
        </flux:table>
    </flux:card.content>
</flux:card>
