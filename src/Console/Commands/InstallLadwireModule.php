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
        $this->info('⚙️  Run "php artisan vendor:publish --tag=ladwire-views" to publish views.');
    }

    protected function installDashboard()
    {
        $this->info('Installing Dashboard module...');
        
        // Create controller
        $this->createController('Dashboard', 'dashboard');
        
        // Create route
        $this->addRoute('dashboard', 'DashboardController');
        
        $this->info('✅ Dashboard module installed');
    }

    protected function installUserManagement()
    {
        $this->info('Installing User Management module...');
        
        // Create controller
        $this->createController('UserManagement', 'user-management');
        
        // Create route
        $this->addRoute('user-management', 'UserManagementController');
        
        $this->info('✅ User Management module installed');
    }

    protected function installSettings()
    {
        $this->info('Installing Settings module...');
        
        // Create controller
        $this->createController('Settings', 'settings');
        
        // Create route
        $this->addRoute('settings', 'SettingsController');
        
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
        
        $routeContent = "Route::get('/{$route}', {$controller}::class)->name('{$route}');\n";
        File::append($routesPath, $routeContent);
        
        $this->info("Added route: /{$route}");
    }

    protected function getControllerStub($name, $route)
    {
        return <<<PHP
<?php

namespace App\Http\Controllers;

use Ladbu\LaravelLadwireModule\Http\Livewire\\{$name};

class {$name}Controller extends Controller
{
    public function __invoke()
    {
        return view('ladwire.{$route}', [
            'component' => '{$name}',
        ]);
    }
}
PHP;
    }
}
