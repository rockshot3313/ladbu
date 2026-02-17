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
<div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6 mb-6">
    <div class="flex items-center justify-between">
        <input 
            type="text" 
            wire:model.live="search" 
            placeholder="Search users..." 
            class="max-w-xs px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        
        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center">
            <flux:icon name="layout-grid" class="w-4 h-4 mr-2" />
            Add User
        </button>
    </div>
</div>

<!-- Users Table -->
<div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg">
    <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
        <flux:heading>Users</flux:heading>
        <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">Manage application users</flux:text>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-zinc-50 dark:bg-zinc-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach($users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $user['name'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $user['email'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $user['role'] === 'Admin' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-300' }}">
                                {{ $user['role'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">{{ $user['created_at'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                            <div class="flex space-x-2">
                                <button class="p-2 border border-zinc-300 dark:border-zinc-600 rounded hover:bg-zinc-50 dark:hover:bg-zinc-700">
                                    <flux:icon name="folder-git-2" class="w-4 h-4" />
                                </button>
                                <button 
                                    class="p-2 border border-zinc-300 dark:border-zinc-600 rounded hover:bg-zinc-50 dark:hover:bg-zinc-700"
                                    wire:click="deleteUser({{ $user['id'] }})"
                                >
                                    <flux:icon name="book-open-text" class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
