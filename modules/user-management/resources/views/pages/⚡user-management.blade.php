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
    
    // Filter properties
    public $dateRange = '30';
    public $comparisonPeriod = 'previous';
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
        $this->reset(['dateRange', 'comparisonPeriod', 'statusFilter', 'roleFilter']);
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

        <flux:navbar class="max-lg:hidden -mb-px">
            <flux:navbar.item href="#" data-current>Dashboard</flux:navbar.item>
            <flux:navbar.item href="#" badge="32">Orders</flux:navbar.item>
            <flux:navbar.item href="#">Catalog</flux:navbar.item>
            <flux:navbar.item href="#">Configuration</flux:navbar.item>
        </flux:navbar>
    </flux:header>

    <flux:sidebar collapsible="mobile" class="lg:hidden bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <flux:sidebar.nav>
            <flux:sidebar.item href="#" data-current>Dashboard</flux:sidebar.item>
            <flux:sidebar.item href="#" badge="32">Orders</flux:sidebar.item>
            <flux:sidebar.item href="#">Catalog</flux:sidebar.item>
            <flux:sidebar.item href="#">Configuration</flux:sidebar.item>
        </flux:sidebar.nav>
    </flux:sidebar>

    <flux:main container>
        <div class="flex justify-between items-center mb-6">
            <div>
                <flux:heading size="xl">User Management</flux:heading>
                <flux:subheading>Manage your application users and their roles.</flux:subheading>
            </div>
            
            <flux:button wire:click="openCreateModal" variant="primary" icon="plus">Add User</flux:button>
        </div>

        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-2">
                    <flux:select wire:model.live="dateRange" size="sm" class="">
                        <option value="7">Last 7 days</option>
                        <option value="14">Last 14 days</option>
                        <option value="30" selected>Last 30 days</option>
                        <option value="60">Last 60 days</option>
                        <option value="90">Last 90 days</option>
                    </flux:select>

                    <flux:subheading class="max-md:hidden whitespace-nowrap">compared to</flux:subheading>

                    <flux:select wire:model.live="comparisonPeriod" size="sm" class="max-md:hidden">
                        <option value="previous" selected>Previous period</option>
                        <option value="same_year">Same period last year</option>
                        <option value="last_month">Last month</option>
                        <option value="last_quarter">Last quarter</option>
                        <option value="last_6_months">Last 6 months</option>
                        <option value="last_12_months">Last 12 months</option>
                    </flux:select>
                </div>

                <flux:separator vertical class="max-lg:hidden mx-2 my-2" />

                <div class="max-lg:hidden flex justify-start items-center gap-2">
                    <flux:subheading class="whitespace-nowrap">Filter by:</flux:subheading>

                    <flux:badge 
                        as="button" 
                        rounded 
                        :color="$statusFilter ? 'blue' : 'zinc'" 
                        :icon="$statusFilter ? 'check' : 'plus'" 
                        size="lg"
                        wire:click="$statusFilter = $statusFilter ? null : 'active'"
                    >
                        Status {{ $statusFilter ? '(' . $statusFilter . ')' : '' }}
                    </flux:badge>
                    
                    <flux:badge 
                        as="button" 
                        rounded 
                        :color="$roleFilter ? 'blue' : 'zinc'" 
                        :icon="$roleFilter ? 'check' : 'plus'" 
                        size="lg"
                        wire:click="$roleFilter = $roleFilter ? null : 'admin'"
                    >
                        Role {{ $roleFilter ? '(' . $roleFilter . ')' : '' }}
                    </flux:badge>
                    
                    <flux:badge 
                        as="button" 
                        rounded 
                        color="red" 
                        icon="x-mark" 
                        size="lg"
                        wire:click="clearFilters"
                        class="{{ !$dateRange && !$statusFilter && !$roleFilter ? 'hidden' : '' }}"
                    >
                        Clear filters
                    </flux:badge>
                </div>
            </div>

            <flux:tabs variant="segmented" class="w-auto! ml-2" size="sm">
                <flux:tab 
                    icon="list-bullet" 
                    icon:variant="outline" 
                    :data-current="$viewMode === 'list'"
                    wire:click="setViewMode('list')"
                />
                <flux:tab 
                    icon="squares-2x2" 
                    icon:variant="outline" 
                    :data-current="$viewMode === 'grid'"
                    wire:click="setViewMode('grid')"
                />
            </flux:tabs>
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
                                <flux:avatar :name="$user->name" size="sm" />
                                <span class="font-medium text-zinc-800 dark:text-white">{{ $user->name }}</span>
                            </div>
                        </flux:table.cell>
                        
                        <flux:table.cell class="max-md:hidden">{{ $user->email }}</flux:table.cell>
                        
                        <flux:table.cell class="max-md:hidden text-zinc-500">
                            {{ $user->created_at->format('M d, Y') }}
                        </flux:table.cell>

                        <flux:table.cell align="end">
                            <flux:dropdown position="bottom" align="end" offset="-15">
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />
                                <flux:menu>
                                    <flux:menu.item wire:click="editUser({{ $user->id }})" icon="pencil-square">Edit</flux:menu.item>
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

                    <div class="flex gap-3">
                        <flux:button type="button" wire:click="closeCreateModal" variant="outline">Cancel</flux:button>
                        <flux:spacer />
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

                    <div class="flex gap-3">
                        <flux:button type="button" wire:click="closeEditModal" variant="outline">Cancel</flux:button>
                        <flux:spacer />
                        <flux:button type="submit" variant="primary">Save Changes</flux:button>
                    </div>
                </div>
            </form>
        </flux:modal>
    </flux:main>
</div>
