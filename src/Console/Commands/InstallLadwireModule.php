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
<flux:grid class="mb-8">
    <flux:card>
        <flux:card.content>
            <div class="flex items-center">
                <flux:icon.users class="w-6 h-6 text-blue-600" />
                <div class="ml-4">
                    <flux:text size="sm" color="gray">Total Users</flux:text>
                    <flux:text size="2xl" weight="semibold">150</flux:text>
                </div>
            </div>
        </flux:card.content>
    </flux:card>

    <flux:card>
        <flux:card.content>
            <div class="flex items-center">
                <flux:icon.check-circle class="w-6 h-6 text-green-600" />
                <div class="ml-4">
                    <flux:text size="sm" color="gray">Active Users</flux:text>
                    <flux:text size="2xl" weight="semibold">45</flux:text>
                </div>
            </div>
        </flux:card.content>
    </flux:card>

    <flux:card>
        <flux:card.content>
            <div class="flex items-center">
                <flux:icon.document-text class="w-6 h-6 text-purple-600" />
                <div class="ml-4">
                    <flux:text size="sm" color="gray">Total Posts</flux:text>
                    <flux:text size="2xl" weight="semibold">320</flux:text>
                </div>
            </div>
        </flux:card.content>
    </flux:card>

    <flux:card>
        <flux:card.content>
            <div class="flex items-center">
                <flux:icon.clock class="w-6 h-6 text-orange-600" />
                <div class="ml-4">
                    <flux:text size="sm" color="gray">New Posts Today</flux:text>
                    <flux:text size="2xl" weight="semibold">12</flux:text>
                </div>
            </div>
        </flux:card.content>
    </flux:card>
</flux:grid>

<!-- Recent Activity -->
<flux:card>
    <flux:card.header>
        <flux:card.title>Recent Activity</flux:card.title>
    </flux:card.header>
    
    <flux:card.content>
        <flux:separator />
        
        <div class="flex items-center justify-between py-4">
            <div class="flex items-center">
                <flux:avatar>JD</flux:avatar>
                <div class="ml-4">
                    <flux:text weight="medium">John Doe</flux:text>
                    <flux:text size="sm" color="gray">Created new post</flux:text>
                </div>
            </div>
            <flux:text size="sm" color="gray">2 minutes ago</flux:text>
        </div>
        
        <flux:separator />
        
        <div class="flex items-center justify-between py-4">
            <div class="flex items-center">
                <flux:avatar>JS</flux:avatar>
                <div class="ml-4">
                    <flux:text weight="medium">Jane Smith</flux:text>
                    <flux:text size="sm" color="gray">Updated profile</flux:text>
                </div>
            </div>
            <flux:text size="sm" color="gray">5 minutes ago</flux:text>
        </div>
    </flux:card.content>
</flux:card>
BLADE;
        }

        // Default view for other components
        return <<<BLADE
<flux:heading>{{ ucfirst(str_replace('-', ' ', str_replace('ladwire-', '', $viewName))) }}</flux:heading>

<flux:card>
    <flux:card.content>
        <flux:text>This is the {$viewName} component. Customize this view to add your functionality.</flux:text>
    </flux:card.content>
</flux:card>
BLADE;
    }
}
