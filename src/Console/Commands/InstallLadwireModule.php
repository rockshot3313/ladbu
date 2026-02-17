<?php

namespace Ladbu\LaravelLadwireModule\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class InstallLadwireModule extends Command
{
    protected $signature = 'ladwire:install {--dashboard} {--user-management} {--settings}';
    protected $description = 'Install Ladwire components into a fresh Laravel project';

    public function handle()
    {
        $this->info('Installing Ladwire Module...');

        // Check if Flux UI is installed
        if (!$this->isFluxInstalled()) {
            $this->error('❌ Flux UI is not installed!');
            $this->line('Please install Flux UI first:');
            $this->line('1. composer require livewire/flux');
            $this->line('2. php artisan flux:install');
            $this->newLine();
            $this->line('After installing Flux UI, run this command again.');
            return Command::FAILURE;
        }

        $modules = [];
        
        if ($this->option('dashboard')) {
            $this->installDashboard();
            $modules[] = 'dashboard';
        }
        
        if ($this->option('user-management')) {
            $this->installUserManagement();
            $modules[] = 'user-management';
        }
        
        if ($this->option('settings')) {
            $this->installSettings();
            $modules[] = 'settings';
        }

        // If no modules specified, install all
        if (empty($modules)) {
            $this->installAllModules();
        }

        $this->newLine();
        $this->info('✅ Ladwire Module installation complete!');
        $this->info('📁 Check your app/Http/Controllers folder for installed controllers.');
        $this->info('📁 Check your routes/web.php for added routes.');
        $this->info('⚙️  Run "php artisan vendor:publish --tag=views" to publish views.');
        
        return Command::SUCCESS;
    }

    protected function isFluxInstalled()
    {
        return class_exists('Livewire\Flux\FluxServiceProvider') || 
               File::exists(base_path('vendor/livewire/flux')) ||
               File::exists(resource_path('views/vendor/flux'));
    }

    protected function installDashboard()
    {
        $this->info('Installing Dashboard module...');
        
        // Create Livewire component
        $this->createLivewireComponent('LadwireDashboard', 'ladwire-dashboard');
        
        // Create controller
        $this->createController('Dashboard', 'dashboard');
        
        // Create route
        $this->addRoute('dashboard', 'DashboardController');
        
        // Create view
        $this->createView('dashboard');
        
        // Add to sidebar
        $this->addSidebarItem('dashboard');
        
        $this->info('✅ Dashboard module installed');
    }

    protected function installUserManagement()
    {
        $this->info('Installing User Management module...');
        
        // Create Livewire component
        $this->createLivewireComponent('LadwireUserManagement', 'ladwire-user-management');
        
        // Create controller
        $this->createController('UserManagement', 'user-management');
        
        // Create route
        $this->addRoute('user-management', 'UserManagementController');
        
        // Create view
        $this->createView('user-management');
        
        // Add to sidebar
        $this->addSidebarItem('user-management');
        
        $this->info('✅ User Management module installed');
    }

    protected function installSettings()
    {
        $this->info('Installing Settings module...');
        
        // Create Livewire component
        $this->createLivewireComponent('LadwireSettings', 'ladwire-settings');
        
        // Create controller
        $this->createController('Settings', 'settings');
        
        // Create route
        $this->addRoute('settings', 'SettingsController');
        
        // Create view
        $this->createView('settings');
        
        // Add to sidebar
        $this->addSidebarItem('settings');
        
        $this->info('✅ Settings module installed');
    }

    protected function installAllModules()
    {
        $this->info('Installing all Ladwire modules...');
        
        $this->installDashboard();
        $this->installUserManagement();
        $this->installSettings();
        
        $this->info('✅ All modules installed');
    }

    protected function createView($name)
    {
        $viewPath = resource_path("views/ladwire/{$name}.blade.php");
        
        // Ensure directory exists
        $viewDir = resource_path("views/ladwire");
        if (!File::exists($viewDir)) {
            File::makeDirectory($viewDir, 0755, true);
        }
        
        $stub = $this->getViewStub($name);
        
        File::put($viewPath, $stub);
        $this->info("Created view: {$viewPath}");
    }

    protected function createLivewireComponent($className, $viewName)
    {
        // Create Livewire component class
        $componentPath = app_path("Livewire/{$className}.php");
        
        // Ensure Livewire directory exists
        $livewireDir = app_path("Livewire");
        if (!File::exists($livewireDir)) {
            File::makeDirectory($livewireDir, 0755, true);
        }
        
        $stub = $this->getLivewireComponentStub($className, $viewName);
        
        File::put($componentPath, $stub);
        $this->info("Created Livewire component: {$componentPath}");
        
        // Create Livewire component view
        $componentViewPath = resource_path("views/livewire/{$viewName}.blade.php");
        
        // Ensure livewire views directory exists
        $livewireViewsDir = resource_path("views/livewire");
        if (!File::exists($livewireViewsDir)) {
            File::makeDirectory($livewireViewsDir, 0755, true);
        }
        
        $viewStub = $this->getLivewireViewStub($viewName);
        
        File::put($componentViewPath, $viewStub);
        $this->info("Created Livewire view: {$componentViewPath}");
    }

    protected function getViewStub($name)
    {
        $title = match($name) {
            'dashboard' => 'Dashboard',
            'user-management' => 'User Management',
            'settings' => 'Settings',
            default => ucfirst($name)
        };
        
        if ($name === 'dashboard') {
            return <<<BLADE
<x-layouts::app :title="__('{$title}')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white mb-2">150</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Total Users</div>
                    </div>
                </div>
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white mb-2">45</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Active Users</div>
                    </div>
                </div>
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white mb-2">320</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Total Posts</div>
                    </div>
                </div>
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white mb-2">12</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">New Today</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
            <div class="h-full overflow-y-auto">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Activity</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-medium mr-3">JD</div>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">John Doe</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Created new post</div>
                            </div>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">2 minutes ago</div>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-medium mr-3">JS</div>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">Jane Smith</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Updated profile</div>
                            </div>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">5 minutes ago</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
BLADE;
        }

        if ($name === 'user-management') {
            return <<<BLADE
<x-layouts::app :title="__('{$title}')">
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
                <flux:table :paginate="\$users">
                    <flux:table.columns>
                        <flux:table.column sortable :sorted="\$sortBy === 'name'" :direction="\$sortDirection" wire:click="sort('name')">Name</flux:table.column>
                        <flux:table.column sortable :sorted="\$sortBy === 'email'" :direction="\$sortDirection" wire:click="sort('email')">Email</flux:table.column>
                        <flux:table.column sortable :sorted="\$sortBy === 'role'" :direction="\$sortDirection" wire:click="sort('role')">Role</flux:table.column>
                        <flux:table.column>Actions</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach (\$users as \$user)
                            <flux:table.row :key="\$user->id">
                                <flux:table.cell class="flex items-center gap-3">
                                    <flux:avatar size="xs">{{ substr(\$user['name'], 0, 1) }}</flux:avatar>
                                    {{ \$user['name'] }}
                                </flux:table.cell>

                                <flux:table.cell>{{ \$user['email'] }}</flux:table.cell>

                                <flux:table.cell>
                                    <flux:badge size="sm" :color="\$user['role'] === 'admin' ? 'primary' : 'secondary'">
                                        {{ \$user['role'] }}
                                    </flux:badge>
                                </flux:table.cell>

                                <flux:table.cell variant="strong">{{ \$user['created_at'] }}</flux:table.cell>

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
BLADE;
        }

        // Default view for other modules
        return <<<BLADE
<x-layouts::app :title="__('{$title}')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
            <div class="h-full overflow-y-auto">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{$title}</h3>
                <div class="text-gray-600 dark:text-gray-400">
                    This is the {$name} module. Customize this view to add your functionality.
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
BLADE;
    }

    protected function addSidebarItem($module)
    {
        $sidebarPath = resource_path('views/layouts/app/sidebar.blade.php');
        
        if (!File::exists($sidebarPath)) {
            $this->warn("Sidebar file not found: {$sidebarPath}");
            return;
        }

        $sidebarContent = File::get($sidebarPath);
        
        $moduleInfo = $this->getModuleInfo($module);
        $sidebarItem = $this->getSidebarItem($moduleInfo);
        
        // Find the position to insert the sidebar item (after the dashboard item)
        $pattern = '/(<flux:sidebar\.item[^>]*>Dashboard<\/flux:sidebar\.item>)/';
        
        if (preg_match($pattern, $sidebarContent)) {
            $sidebarItemWithMarkers = "<!-- Ladwire Module: {$module} -->\n                    " . $sidebarItem . "\n                    <!-- END Ladwire Module: {$module} -->";
            $newSidebarContent = preg_replace($pattern, '$1' . "\n                    " . $sidebarItemWithMarkers, $sidebarContent);
            File::put($sidebarPath, $newSidebarContent);
            $this->info("Added sidebar item for {$moduleInfo['name']}");
        } else {
            // If dashboard item not found, add to the Platform group
            $platformGroupPattern = '/(<flux:sidebar\.group[^>]*heading="[^"]*Platform[^"]*"[^>]*>)/';
            if (preg_match($platformGroupPattern, $sidebarContent)) {
                $sidebarItemWithMarkers = "<!-- Ladwire Module: {$module} -->\n                    " . $sidebarItem . "\n                    <!-- END Ladwire Module: {$module} -->";
                $newSidebarContent = preg_replace($platformGroupPattern, '$1' . "\n                    " . $sidebarItemWithMarkers, $sidebarContent);
                File::put($sidebarPath, $newSidebarContent);
                $this->info("Added sidebar item for {$moduleInfo['name']}");
            }
        }
    }

    protected function getModuleInfo($module)
    {
        return match($module) {
            'dashboard' => [
                'name' => 'Dashboard',
                'route' => 'dashboard',
                'icon' => 'home',
            ],
            'user-management' => [
                'name' => 'User Management',
                'route' => 'user-management',
                'icon' => 'users',
            ],
            'settings' => [
                'name' => 'Settings',
                'route' => 'settings',
                'icon' => 'cog',
            ],
            default => [
                'name' => ucfirst($module),
                'route' => $module,
                'icon' => 'folder-git-2',
            ]
        };
    }

    protected function getSidebarItem($moduleInfo)
    {
        return "<flux:sidebar.item icon=\"{$moduleInfo['icon']}\" :href=\"route('{$moduleInfo['route']}')\" :current=\"request()->routeIs('{$moduleInfo['route']}')\" wire:navigate>\n                        {{ __('{$moduleInfo['name']}') }}\n                    </flux:sidebar.item>";
    }

    protected function addRoute($name, $route)
    {
        $webRoutesPath = base_path('routes/web.php');
        
        if (File::exists($webRoutesPath)) {
            $routeContent = File::get($webRoutesPath);
            
            // Add use statement if not present
            $controllerClass = $name . 'Controller';
            if (!str_contains($routeContent, "use App\\Http\\Controllers\\{$controllerClass};")) {
                $routeContent = str_replace(
                    "use Illuminate\\Support\\Facades\\Route;",
                    "use Illuminate\\Support\\Facades\\Route;\nuse App\\Http\\Controllers\\{$controllerClass};",
                    $routeContent
                );
            }
            
            // Add route with unique identifiers
            $routeLine = "\n// Ladwire Module: {$route}\nRoute::get('/{$route}', {$controllerClass}::class)->name('{$route}'); // END Ladwire Module: {$route}";
            $routeContent .= $routeLine;
            
            File::put($webRoutesPath, $routeContent);
            $this->info("Added route: /{$route}");
        }
    }

    protected function createController($name, $route)
    {
        $controllerPath = app_path("Http/Controllers/{$name}Controller.php");
        
        $stub = $this->getControllerStub($name, $route);
        
        File::put($controllerPath, $stub);
        $this->info("Created: {$controllerPath}");
    }

    protected function getControllerStub($name, $route)
    {
        if ($name === 'Dashboard') {
            return <<<PHP
<?php

namespace App\Http\Controllers;

class {$name}Controller extends Controller
{
    public function __invoke()
    {
        \$stats = [
            'total_users' => 150,
            'active_users' => 45,
            'total_posts' => 320,
            'new_posts_today' => 12,
        ];

        \$recentActivity = [
            ['user' => 'John Doe', 'action' => 'Created new post', 'time' => '2 minutes ago'],
            ['user' => 'Jane Smith', 'action' => 'Updated profile', 'time' => '5 minutes ago'],
            ['user' => 'Bob Johnson', 'action' => 'Deleted comment', 'time' => '10 minutes ago'],
        ];

        return view('ladwire.{$route}', compact('stats', 'recentActivity'));
    }
}
PHP;
        }

        if ($name === 'UserManagement') {
            return <<<PHP
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class {$name}Controller extends Controller
{
    public function __invoke(Request \$request): View|RedirectResponse
    {
        // Mock data - replace with actual database query
        \$users = collect([
            (object)['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'role' => 'admin', 'created_at' => '2024-01-15'],
            (object)['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'role' => 'user', 'created_at' => '2024-01-16'],
            (object)['id' => 3, 'name' => 'Bob Johnson', 'email' => 'bob@example.com', 'role' => 'user', 'created_at' => '2024-01-17'],
        ]);

        return view('ladwire.{$route}', compact('users'));
    }
}
PHP;
        }

        return <<<PHP
<?php

namespace App\Http\Controllers;

class {$name}Controller extends Controller
{
    public function __invoke()
    {
        return view('ladwire.{$route}');
    }
}
PHP;
    }

    protected function getLivewireComponentStub($className, $viewName)
    {
        return <<<PHP
<?php

namespace App\Livewire;

use Livewire\Component;

class {$className} extends Component
{
    public function mount(): void
    {
        // Initialize component data
    }

    public function render()
    {
        return view('livewire.{$viewName}');
    }
}
PHP;
    }

    protected function getLivewireViewStub($viewName)
    {
        if ($viewName === 'ladwire-dashboard') {
            return <<<BLADE
<div>
    <flux:heading>Dashboard</flux:heading>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                    <flux:icon name="layout-grid" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div class="ml-4">
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">Total Users</div>
                    <div class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">150</div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                    <flux:icon name="chevrons-up-down" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div class="ml-4">
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">Active Users</div>
                    <div class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">45</div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                    <flux:icon name="folder-git-2" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                </div>
                <div class="ml-4">
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">Total Posts</div>
                    <div class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">320</div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                    <flux:icon name="book-open-text" class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                </div>
                <div class="ml-4">
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">New Posts Today</div>
                    <div class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">12</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <flux:heading>Recent Activity</flux:heading>
        </div>
        
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-center justify-between py-3 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-zinc-100 dark:bg-zinc-700 rounded-full flex items-center justify-center">
                            <span class="text-sm font-medium text-zinc-600 dark:text-zinc-300">JD</span>
                        </div>
                        <div class="ml-4">
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">John Doe</div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">Created new post</div>
                        </div>
                    </div>
                    <div class="text-sm text-zinc-500 dark:text-zinc-400">2 minutes ago</div>
                </div>
                
                <div class="flex items-center justify-between py-3">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-zinc-100 dark:bg-zinc-700 rounded-full flex items-center justify-center">
                            <span class="text-sm font-medium text-zinc-600 dark:text-zinc-300">JS</span>
                        </div>
                        <div class="ml-4">
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">Jane Smith</div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">Updated profile</div>
                        </div>
                    </div>
                    <div class="text-sm text-zinc-500 dark:text-zinc-400">5 minutes ago</div>
                </div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400">10 minutes ago</div>
            </div>
        </div>
    </div>
</div>
BLADE;
        }

        // Default view for other components
        return <<<BLADE
<flux:heading>{{ ucfirst(str_replace('-', ' ', str_replace('ladwire-', '', $viewName))) }}</flux:heading>

<div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6">
    <flux:text>This is the {$viewName} component. Customize this view to add your functionality.</flux:text>
</div>
BLADE;
    }
}
