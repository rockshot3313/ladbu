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

    protected function getViewStub($name)
    {
        $componentMap = [
            'dashboard' => 'laravel-ladwire-dashboard::dashboard',
            'user-management' => 'laravel-ladwire-user-management::user-management',
            'settings' => 'laravel-ladwire-settings::settings',
        ];
        
        $component = $componentMap[$name] ?? "laravel-ladwire-{$name}::{$name}";
        
        return <<<BLADE
@extends('layouts.app')

@section('content')
    <flux:prose>
        <livewire:{$component} />
    </flux:prose>
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
}
