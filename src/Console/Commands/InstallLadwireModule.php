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

        // Default view for other modules
        return <<<BLADE
<x-layouts::app :title="__('{$title}')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
            <div class="h-full overflow-y-auto">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ $title }}</h3>
                <div class="text-gray-600 dark:text-gray-400">
                    This is the {$name} module. Customize this view to add your functionality.
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
BLADE;
    }

    protected function createController($name, $route)
    {
        $controllerPath = app_path("Http/Controllers/{$name}Controller.php");
        
        $stub = $this->getControllerStub($name, $route);
        
        File::put($controllerPath, $stub);
        $this->info("Created: {$controllerPath}");
    }

    protected function addRoute($route, $controller)
    {
        $routesPath = base_path('routes/web.php');
        
        if (!File::exists($routesPath)) {
            File::put($routesPath, "<?php\n\nuse Illuminate\Support\Facades\Route;\n\n");
        }
        
        // Check if controller is already imported
        $routesContent = File::get($routesPath);
        $controllerClass = "App\\Http\\Controllers\\{$controller}";
        
        if (!str_contains($routesContent, "use {$controllerClass};")) {
            // Add import at the top after existing imports
            $lines = explode("\n", $routesContent);
            $importLine = "use {$controllerClass};";
            
            // Find the last use statement and add after it
            $lastUseIndex = -1;
            foreach ($lines as $index => $line) {
                if (str_starts_with(trim($line), 'use ') && !str_contains($line, 'function')) {
                    $lastUseIndex = $index;
                }
            }
            
            if ($lastUseIndex >= 0) {
                array_splice($lines, $lastUseIndex + 1, 0, $importLine);
            } else {
                // Add after the opening PHP tag
                array_splice($lines, 1, 0, $importLine);
            }
            
            File::put($routesPath, implode("\n", $lines));
        }
        
        // Check if route already exists
        $routePattern = "Route::get('/{$route}'";
        if (!str_contains($routesContent, $routePattern)) {
            $routeContent = "\nRoute::get('/{$route}', {$controller}::class)->name('{$route}');";
            File::append($routesPath, $routeContent);
            $this->info("Added route: /{$route}");
        } else {
            $this->info("Route already exists: /{$route}");
        }
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
