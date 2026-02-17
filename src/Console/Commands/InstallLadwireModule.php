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
        
        $stub = $this->getLivewireComponentStub($className, $viewName);
        
        File::put($componentPath, $stub);
        $this->info("Created Livewire component: {$componentPath}");
        
        // Create Livewire component view
        $componentViewPath = resource_path("views/livewire/{$viewName}.blade.php");
        
        $viewStub = $this->getLivewireViewStub($viewName);
        
        File::put($componentViewPath, $viewStub);
        $this->info("Created Livewire view: {$componentViewPath}");
    }

    protected function getViewStub($name)
    {
        $componentMap = [
            'dashboard' => 'ladwire-dashboard',
            'user-management' => 'ladwire-user-management',
            'settings' => 'ladwire-settings',
        ];
        
        $component = $componentMap[$name] ?? "ladwire-{$name}";
        
        return <<<BLADE
@extends('layouts.app')

@section('content')
    <flux:main>
        <livewire:{$component} />
    </flux:main>
@endsection
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
            <div class="flex items-center justify-between py-3 border-b border-zinc-200 dark:border-zinc-700 last:border-b-0">
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
            
            <div class="flex items-center justify-between py-3 border-b border-zinc-200 dark:border-zinc-700 last:border-b-0">
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
            
            <div class="flex items-center justify-between py-3">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-zinc-100 dark:bg-zinc-700 rounded-full flex items-center justify-center">
                        <span class="text-sm font-medium text-zinc-600 dark:text-zinc-300">BJ</span>
                    </div>
                    <div class="ml-4">
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">Bob Johnson</div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">Deleted comment</div>
                    </div>
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
