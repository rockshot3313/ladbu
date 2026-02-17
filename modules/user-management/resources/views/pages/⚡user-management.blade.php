<?php
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Flux\Flux;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public $name = '';
    public $email = '';
    public $password = '';
    public $editingUser = null;
    public $showCreateModal = false;
    public $showEditModal = false;
    public $dropdownOpen = null;
    
    // Filter properties
    public $dateRange = '30';
    public $statusFilter = null;
    public $roleFilter = null;
    public $viewMode = 'list'; // 'list' or 'grid'
    
    // Summary statistics
    public $totalUsers = 0;
    public $activeUsers = 0;
    public $newUsersThisMonth = 0;
    public $filteredUsersCount = 0;

    // Reset pagination when searching or filtering
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedDateRange()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedRoleFilter()
    {
        $this->resetPage();
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function clearFilters()
    {
        $this->reset(['dateRange', 'statusFilter', 'roleFilter']);
        $this->resetPage();
    }

    private function getFilteredUsers()
    {
        $query = User::query();

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        // Date range filter
        if ($this->dateRange) {
            $days = (int) $this->dateRange;
            $query->where('created_at', '>=', now()->subDays($days));
        }

        // Status filter (assuming users have an 'active' status field)
        if ($this->statusFilter) {
            if ($this->statusFilter === 'active') {
                $query->where('email_verified_at', '!=', null);
            } elseif ($this->statusFilter === 'inactive') {
                $query->where('email_verified_at', '=', null);
            }
        }

        // Role filter (assuming users have a 'role' field)
        if ($this->roleFilter) {
            $query->where('role', $this->roleFilter);
        }

        $this->filteredUsersCount = $query->count();
        return $query->paginate(10);
    }

    private function calculateStatistics()
    {
        $this->totalUsers = User::count();
        $this->activeUsers = User::where('email_verified_at', '!=', null)->count();
        $this->newUsersThisMonth = User::where('created_at', '>=', now()->startOfMonth())->count();
    }

    public function createUser()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
        ]);

        $this->reset(['name', 'email', 'password', 'showCreateModal']);
        Flux::toast('User created successfully.');
    }

    public function editUser($id)
    {
        $this->editingUser = User::findOrFail($id);
        $this->name = $this->editingUser->name;
        $this->email = $this->editingUser->email;
        $this->showEditModal = true;
    }

    public function updateUser()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->editingUser->id,
        ]);

        $this->editingUser->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        $this->reset(['name', 'email', 'editingUser', 'showEditModal']);
        Flux::toast('User updated successfully.');
    }

    public function deleteUser($id)
    {
        User::findOrFail($id)->delete();
        Flux::toast('User deleted successfully.');
    }

    public function openCreateModal()
    {
        $this->reset(['name', 'email', 'password']);
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->reset(['name', 'email', 'password']);
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->reset(['name', 'email', 'editingUser']);
    }

    public function toggleDropdown($id)
    {
        $this->dropdownOpen = $this->dropdownOpen === $id ? null : $id;
    }

    // Replace render() with with() to pass data to the view
    public function with(): array
    {
        $this->calculateStatistics();
        $users = $this->getFilteredUsers();
        
        return [
            'users' => $users,
            'totalUsers' => $this->totalUsers,
            'activeUsers' => $this->activeUsers,
            'newUsersThisMonth' => $this->newUsersThisMonth,
            'filteredUsersCount' => $this->filteredUsersCount,
        ];
    }
}; ?>

<div>
    <flux:header sticky container class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-600">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" />
        
        <div class="flex-1" />
        
        <flux:button wire:click="openCreateModal" variant="primary" icon="plus">Add User</flux:button>
    </flux:header>


    <flux:main container>
        <div class="mb-6">
            <flux:heading size="xl">User Management</flux:heading>
            <flux:subheading>Manage your application users and their roles.</flux:subheading>
        </div>

        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center gap-2">
                <flux:select wire:model.live="dateRange" size="sm" class="">
                    <option value="7">Last 7 days</option>
                    <option value="14">Last 14 days</option>
                    <option value="30" selected>Last 30 days</option>
                    <option value="60">Last 60 days</option>
                    <option value="90">Last 90 days</option>
                </flux:select>

                <flux:separator vertical class="max-lg:hidden mx-2 my-2" />

                <div class="max-lg:hidden flex justify-start items-center gap-2">
                    <flux:subheading class="whitespace-nowrap">Filter by:</flux:subheading>

                    <button 
                        wire:click="$statusFilter = $statusFilter ? null : 'active'"
                        class="px-3 py-1 rounded-full text-sm font-medium {{ $statusFilter ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-200' }}"
                    >
                        Status {{ $statusFilter ? '(' . $statusFilter . ')' : '' }}
                    </button>
                    
                    <button 
                        wire:click="$roleFilter = $roleFilter ? null : 'admin'"
                        class="px-3 py-1 rounded-full text-sm font-medium {{ $roleFilter ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-200' }}"
                    >
                        Role {{ $roleFilter ? '(' . $roleFilter . ')' : '' }}
                    </button>
                    
                    <button 
                        wire:click="clearFilters"
                        class="px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 {{ !$dateRange && !$statusFilter && !$roleFilter ? 'hidden' : '' }}"
                    >
                        Clear filters
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button 
                    wire:click="setViewMode('list')" 
                    class="p-2 rounded {{ $viewMode === 'list' ? 'bg-zinc-200 dark:bg-zinc-700' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800' }}"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <button 
                    wire:click="setViewMode('grid')" 
                    class="p-2 rounded {{ $viewMode === 'grid' ? 'bg-zinc-200 dark:bg-zinc-700' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800' }}"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="mb-4 flex gap-4">
            <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Search users..." class="max-w-xs" />
        </div>

        <!-- Summary Statistics -->
        <div class="flex gap-6 mb-6">
            <div class="relative flex-1 rounded-lg px-6 py-4 bg-zinc-50 dark:bg-zinc-700">
                <flux:subheading>Total Users</flux:subheading>
                <flux:heading size="xl" class="mb-2">{{ $totalUsers }}</flux:heading>
                <div class="flex items-center gap-1 font-medium text-sm text-blue-600 dark:text-blue-400">
                    <flux:icon icon="users" variant="micro" />
                    All registered users
                </div>
            </div>
            
            <div class="relative flex-1 rounded-lg px-6 py-4 bg-zinc-50 dark:bg-zinc-700 max-md:hidden">
                <flux:subheading>Active Users</flux:subheading>
                <flux:heading size="xl" class="mb-2">{{ $activeUsers }}</flux:heading>
                <div class="flex items-center gap-1 font-medium text-sm text-green-600 dark:text-green-400">
                    <flux:icon icon="check-circle" variant="micro" />
                    {{ $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 1) : 0 }}% active rate
                </div>
            </div>
            
            <div class="relative flex-1 rounded-lg px-6 py-4 bg-zinc-50 dark:bg-zinc-700 max-lg:hidden">
                <flux:subheading>New This Month</flux:subheading>
                <flux:heading size="xl" class="mb-2">{{ $newUsersThisMonth }}</flux:heading>
                <div class="flex items-center gap-1 font-medium text-sm text-amber-600 dark:text-amber-400">
                    <flux:icon icon="calendar" variant="micro" />
                    {{ now()->format('F') }} {{ now->year }}
                </div>
            </div>
            
            <div class="relative flex-1 rounded-lg px-6 py-4 bg-zinc-50 dark:bg-zinc-700">
                <flux:subheading>Filtered Results</flux:subheading>
                <flux:heading size="xl" class="mb-2">{{ $filteredUsersCount }}</flux:heading>
                <div class="flex items-center gap-1 font-medium text-sm text-purple-600 dark:text-purple-400">
                    <flux:icon icon="funnel" variant="micro" />
                    {{ $filteredUsersCount !== $totalUsers ? 'Filtered' : 'All users' }}
                </div>
            </div>
        </div>

        <flux:table :paginate="$users">
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column class="max-md:hidden">Email</flux:table.column>
                <flux:table.column class="max-md:hidden">Joined</flux:table.column>
                <flux:table.column align="end">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($users as $user)
                    <flux:table.row :key="$user->id">
                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-600 flex items-center justify-center text-sm font-medium text-zinc-600 dark:text-zinc-300">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="font-medium text-zinc-800 dark:text-white">{{ $user->name }}</span>
                            </div>
                        </flux:table.cell>
                        
                        <flux:table.cell class="max-md:hidden">{{ $user->email }}</flux:table.cell>
                        
                        <flux:table.cell class="max-md:hidden text-zinc-500">
                            {{ $user->created_at->format('M d, Y') }}
                        </flux:table.cell>

                        <flux:table.cell align="end">
                            <div class="relative">
                                <button 
                                    wire:click="$toggleDropdown('user-' . $user->id)"
                                    class="p-1 rounded hover:bg-zinc-100 dark:hover:bg-zinc-800"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                    </svg>
                                </button>
                                
                                @if ($this->dropdownOpen === 'user-' . $user->id)
                                    <div class="absolute right-0 mt-1 w-48 bg-white dark:bg-zinc-800 rounded-md shadow-lg border border-zinc-200 dark:border-zinc-700 z-50">
                                        <button 
                                            wire:click="editUser({{ $user->id }})"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-700 flex items-center gap-2"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit
                                        </button>
                                        <button 
                                            wire:click="deleteUser({{ $user->id }})"
                                            wire:confirm="Are you sure you want to delete this user?"
                                            class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-zinc-100 dark:hover:bg-zinc-700 flex items-center gap-2"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        <div class="mt-6">
            <div class="flex justify-between items-center mb-4">
                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">
                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $filteredUsersCount }} users
                    {{ $filteredUsersCount !== $totalUsers ? ' (filtered from ' . $totalUsers . ' total)' : '' }}
                </flux:text>
                
                <flux:select wire:model.live="users.perPage" size="sm" class="w-32">
                    <option value="10">10 per page</option>
                    <option value="25">25 per page</option>
                    <option value="50">50 per page</option>
                    <option value="100">100 per page</option>
                </flux:select>
            </div>
            
            <flux:pagination :paginator="$users" />
        </div>

        <!-- Create User Modal -->
        <flux:modal name="create-user" wire:model="showCreateModal" class="md:w-96">
            <form wire:submit="createUser">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">Create User</flux:heading>
                        <flux:text class="mt-2">Add a new user to the system.</flux:text>
                    </div>

                    <flux:input 
                        wire:model="name" 
                        label="Name" 
                        placeholder="Enter user name" 
                        required
                    />

                    <flux:input 
                        wire:model="email" 
                        label="Email" 
                        type="email" 
                        placeholder="user@example.com" 
                        required
                    />

                    <flux:input 
                        wire:model="password" 
                        label="Password" 
                        type="password" 
                        placeholder="Enter password" 
                        required
                    />

                    <div class="flex gap-3 justify-end">
                        <flux:button type="button" wire:click="closeCreateModal" variant="outline">Cancel</flux:button>
                        <flux:button type="submit" variant="primary">Create User</flux:button>
                    </div>
                </div>
            </form>
        </flux:modal>

        <!-- Edit User Modal -->
        <flux:modal name="edit-profile" wire:model="showEditModal" class="md:w-96">
            <form wire:submit="updateUser">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">Update User</flux:heading>
                        <flux:text class="mt-2">Make changes to user details.</flux:text>
                    </div>

                    <flux:input 
                        wire:model="name" 
                        label="Name" 
                        placeholder="Enter user name" 
                        required
                    />

                    <flux:input 
                        wire:model="email" 
                        label="Email" 
                        type="email" 
                        placeholder="user@example.com" 
                        required
                    />

                    <div class="flex gap-3 justify-end">
                        <flux:button type="button" wire:click="closeEditModal" variant="outline">Cancel</flux:button>
                        <flux:button type="submit" variant="primary">Save Changes</flux:button>
                    </div>
                </div>
            </form>
        </flux:modal>
    </flux:main>
</div>
