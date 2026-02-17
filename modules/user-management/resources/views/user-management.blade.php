<x-layouts::app :title="__('User Management')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
            <div class="h-full overflow-y-auto">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">User Management</h3>
                
                <!-- Search and Actions -->
                <div class="flex items-center justify-between mb-6">
                    <input 
                        type="text" 
                        placeholder="Search users..." 
                        class="px-4 py-2 border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add User
                    </button>
                </div>

                <!-- Flux Table -->
                <flux:table :paginate="$users">
                    <flux:table.columns>
                        <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Name</flux:table.column>
                        <flux:table.column sortable :sorted="$sortBy === 'email'" :direction="$sortDirection" wire:click="sort('email')">Email</flux:table.column>
                        <flux:table.column sortable :sorted="$sortBy === 'role'" :direction="$sortDirection" wire:click="sort('role')">Role</flux:table.column>
                        <flux:table.column>Actions</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($users as $user)
                            <flux:table.row :key="$user->id">
                                <flux:table.cell class="flex items-center gap-3">
                                    <flux:avatar size="xs">{{ substr($user['name'], 0, 1) }}</flux:avatar>
                                    {{ $user['name'] }}
                                </flux:table.cell>

                                <flux:table.cell>{{ $user['email'] }}</flux:table.cell>

                                <flux:table.cell>
                                    <flux:badge size="sm" :color="$user['role'] === 'admin' ? 'primary' : 'secondary'">
                                        {{ $user['role'] }}
                                    </flux:badge>
                                </flux:table.cell>

                                <flux:table.cell variant="strong">{{ $user['created_at'] }}</flux:table.cell>

                                <flux:table.cell>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>
        </div>
    </div>
</x-layouts::app>
