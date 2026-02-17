<?php
use App\Models\User;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Flux\Flux;

new class extends Component {
    use WithPagination;

    public string $search = '';

    // Reset pagination when searching
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function deleteUser($id)
    {
        User::findOrFail($id)->delete();
        Flux::toast('User deleted successfully.');
    }

    public function with(): array
    {
        return [
            'users' => User::where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
                ->paginate(10),
        ];
    }
}; ?>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <flux:heading size="xl">User Management</flux:heading>
            <flux:subheading>Manage your application users and their roles.</flux:subheading>
        </div>
        
        <flux:modal.trigger name="create-user">
            <flux:button variant="primary" icon="plus">Add User</flux:button>
        </flux:modal.trigger>
    </div>

    <div class="mb-4 flex gap-4">
        <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Search users..." class="max-w-xs" />
    </div>

    <flux:table :paginate="$users">
        <flux:table.columns>
            <flux:table.column>Name</flux:table.column>
            <flux:table.column>Email</flux:table.column>
            <flux:table.column>Joined</flux:table.column>
            <flux:table.column align="end">Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($users as $user)
                <flux:table.row :key="$user->id">
                    <flux:table.cell>
                        <div class="flex items-center gap-3">
                            <flux:avatar :name="$user->name" size="sm" />
                            <span class="font-medium text-zinc-800 dark:text-white">{{ $user->name }}</span>
                        </div>
                    </flux:table.cell>
                    
                    <flux:table.cell>{{ $user->email }}</flux:table.cell>
                    
                    <flux:table.cell class="text-zinc-500">
                        {{ $user->created_at->format('M d, Y') }}
                    </flux:table.cell>

                    <flux:table.cell align="end">
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                            <flux:menu>
                                <flux:menu.item icon="pencil-square">Edit</flux:menu.item>
                                <flux:menu.item 
                                    wire:click="deleteUser({{ $user->id }})" 
                                    wire:confirm="Are you sure you want to delete this user?" 
                                    variant="danger" 
                                    icon="trash"
                                >
                                    Delete
                                </flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <div class="mt-6">
        <flux:pagination :paginator="$users" />
    </div>
</div>
