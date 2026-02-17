<?php

namespace Ladbu\LaravelLadwireModule\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeLadwireModule extends Command
{
    protected $signature = 'make:ladwire-module {name : The name of the module}';
    protected $description = 'Create a new Ladwire module with Laravel best practices';

    public function handle()
    {
        $name = $this->argument('name');
        
        if (empty($name)) {
            $this->error('Module name is required.');
            return Command::FAILURE;
        }

        $this->info("Creating Ladwire module: {$name}");
        
        $this->createModuleStructure($name);
        $this->createComposerJson($name);
        $this->createServiceProvider($name);
        $this->createConfig($name);
        $this->createLivewireComponent($name);
        $this->createViews($name);
        $this->createRoutes($name);
        $this->createTests($name);
        
        $this->updateMainPackage($name);
        
        $this->newLine();
        $this->info("✅ Module '{$name}' created successfully!");
        $this->info("📁 Location: modules/" . Str::kebab($name));
        $this->info("📝 Next steps:");
        $this->line("1. Implement your module logic in the Livewire component");
        $this->line("2. Customize the views with your specific features");
        $this->line("3. Add tests for your functionality");
        $this->line("4. Update the main package when ready to publish");
        
        return Command::SUCCESS;
    }

    protected function createModuleStructure($name)
    {
        $modulePath = base_path("modules/" . Str::kebab($name));
        
        $directories = [
            $modulePath,
            $modulePath . "/config",
            $modulePath . "/resources/views/livewire",
            $modulePath . "/routes",
            $modulePath . "/src/Http/Livewire",
            $modulePath . "/tests/Feature",
        ];

        foreach ($directories as $directory) {
            File::makeDirectory($directory, 0755, true);
        }
    }

    protected function createComposerJson($name)
    {
        $kebabName = Str::kebab($name);
        $pascalName = Str::studly($name);
        
        $content = [
            "name" => "ladbu/laravel-ladwire-{$kebabName}",
            "description" => "The {$kebabName} module for Laravel Ladwire",
            "type" => "laravel-package",
            "keywords" => ["laravel", "ladwire", $kebabName],
            "license" => "MIT",
            "authors" => [
                [
                    "name" => "Ladbu",
                    "email" => "geraldalbacite.org@gmail.com"
                ]
            ],
            "require" => [
                "php" => "^8.2",
                "illuminate/support" => "^11.0|^12.0",
                "livewire/livewire" => "^3.0|^4.0",
                "livewire/flux" => "^1.0|^2.0"
            ],
            "require-dev" => [
                "orchestra/testbench" => "^7.0|^8.0",
                "phpunit/phpunit" => "^9.0|^10.0"
            ],
            "autoload" => [
                "psr-4" => [
                    "Ladbu\\LaravelLadwire{$pascalName}\\" => "src/"
                ]
            ],
            "autoload-dev" => [
                "psr-4" => [
                    "Ladbu\\LaravelLadwire{$pascalName}\\Tests\\" => "tests/"
                ]
            ],
            "extra" => [
                "laravel" => [
                    "providers" => [
                        "Ladbu\\LaravelLadwire{$pascalName}\\{$pascalName}ServiceProvider"
                    ]
                ]
            ],
            "minimum-stability" => "stable",
            "prefer-stable" => true
        ];

        $path = base_path("modules/" . Str::kebab($name) . "/composer.json");
        File::put($path, json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    protected function createServiceProvider($name)
    {
        $kebabName = Str::kebab($name);
        $pascalName = Str::studly($name);
        
        $content = <<<PHP
<?php

namespace Ladbu\\LaravelLadwire{$pascalName};

use Illuminate\\Support\\ServiceProvider;
use Illuminate\\Support\\Facades\\Route;
use Livewire\\Livewire;

class {$pascalName}ServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        \$this->mergeConfigFrom(
            __DIR__.'/../config/{$kebabName}.php',
            'laravel-ladwire-{$kebabName}'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        \$this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-ladwire-{$kebabName}');
        
        \$this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        if (\$this->app->runningInConsole()) {
            \$this->publishes([
                __DIR__.'/../config/{$kebabName}.php' => config_path('laravel-ladwire-{$kebabName}.php'),
            ], 'config');

            \$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-ladwire-{$kebabName}'),
            ], 'views');
        }

        \$this->registerLadwireComponents();
    }

    /**
     * Register Ladwire components.
     */
    protected function registerLadwireComponents(): void
    {
        Livewire::component('laravel-ladwire-{$kebabName}::{$kebabName}', \\Ladbu\\LaravelLadwire{$pascalName}\\Http\\Livewire\\{$pascalName}::class);
    }
}
PHP;

        $path = base_path("modules/" . Str::kebab($name) . "/src/{$pascalName}ServiceProvider.php");
        File::put($path, $content);
    }

    protected function createConfig($name)
    {
        $kebabName = Str::kebab($name);
        $upperName = Str::upper(Str::snake($name));
        
        $content = <<<PHP
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Module Enabled
    |--------------------------------------------------------------------------
    |
    | This option controls whether the {$kebabName} module is enabled.
    | When set to false, the module's routes and components will not be
    | registered.
    |
    */
    'enabled' => env('LARAVEL_LADWIRE_{$upperName}_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | This option controls the route prefix for the {$kebabName} module.
    | Default is '{$kebabName}'.
    |
    */
    'route_prefix' => env('LARAVEL_LADWIRE_{$upperName}_ROUTE_PREFIX', '{$kebabName}'),

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | This option controls the middleware applied to module routes.
    |
    */
    'middleware' => ['web', 'auth'],
];
PHP;

        $path = base_path("modules/" . Str::kebab($name) . "/config/{$kebabName}.php");
        File::put($path, $content);
    }

    protected function createLivewireComponent($name)
    {
        $kebabName = Str::kebab($name);
        $pascalName = Str::studly($name);
        
        $content = <<<PHP
<?php

namespace Ladbu\\LaravelLadwire{$pascalName}\\Http\\Livewire;

use Livewire\\Component;

class {$pascalName} extends Component
{
    public \$data = [];
    public \$loading = false;

    public function mount()
    {
        \$this->data = [
            'title' => '{$pascalName}',
            'description' => 'This is the {$kebabName} module',
            'stats' => [
                'total_items' => 100,
                'active_items' => 45,
                'new_items_today' => 12,
            ]
        ];
    }

    public function refreshData()
    {
        \$this->loading = true;
        
        // Simulate API call or data processing
        sleep(1);
        
        \$this->data['stats']['total_items'] = rand(90, 110);
        \$this->data['stats']['active_items'] = rand(40, 50);
        \$this->data['stats']['new_items_today'] = rand(10, 15);
        
        \$this->loading = false;
        
        \$this->dispatch('data-refreshed');
    }

    public function render()
    {
        return view('laravel-ladwire-{$kebabName}::livewire.{$kebabName}');
    }
}
PHP;

        $path = base_path("modules/" . Str::kebab($name) . "/src/Http/Livewire/{$pascalName}.php");
        File::put($path, $content);
    }

    protected function createViews($name)
    {
        $kebabName = Str::kebab($name);
        $pascalName = Str::studly($name);
        
        // Main Livewire view
        $livewireContent = <<<BLADE
<flux:heading>{{ \$data['title'] }}</flux:heading>

<flux:prose>
    <flux:text>{{ \$data['description'] }}</flux:text>
</flux:prose>

<!-- Stats Grid -->
<flux:grid class="mb-8">
    <flux:card>
        <flux:card.content>
            <div class="flex items-center">
                <flux:icon.chart-bar class="w-6 h-6 text-blue-600" />
                <div class="ml-4">
                    <flux:text size="sm" color="gray">Total Items</flux:text>
                    <flux:text size="2xl" weight="semibold">{{ \$data['stats']['total_items'] }}</flux:text>
                </div>
            </div>
        </flux:card.content>
    </flux:card>

    <flux:card>
        <flux:card.content>
            <div class="flex items-center">
                <flux:icon.check-circle class="w-6 h-6 text-green-600" />
                <div class="ml-4">
                    <flux:text size="sm" color="gray">Active Items</flux:text>
                    <flux:text size="2xl" weight="semibold">{{ \$data['stats']['active_items'] }}</flux:text>
                </div>
            </div>
        </flux:card.content>
    </flux:card>

    <flux:card>
        <flux:card.content>
            <div class="flex items-center">
                <flux:icon.clock class="w-6 h-6 text-orange-600" />
                <div class="ml-4">
                    <flux:text size="sm" color="gray">New Today</flux:text>
                    <flux:text size="2xl" weight="semibold">{{ \$data['stats']['new_items_today'] }}</flux:text>
                </div>
            </div>
        </flux:card.content>
    </flux:card>
</flux:grid>

<!-- Actions -->
<flux:card>
    <flux:card.header>
        <flux:card.title>Actions</flux:card.title>
    </flux:card.header>
    
    <flux:card.content>
        <flux:button 
            variant="primary" 
            wire:click="refreshData"
            wire:loading.attr="disabled"
        >
            <flux:icon.refresh class="w-4 h-4" />
            {{ \$loading ? 'Refreshing...' : 'Refresh Data' }}
        </flux:button>
    </flux:card.content>
</flux:card>

<!-- Notifications -->
@if (session()->has('success'))
    <flux:toast variant="success">
        {{ session('success') }}
    </flux:toast>
@endif
BLADE;

        $livewirePath = base_path("modules/" . Str::kebab($name) . "/resources/views/livewire/{$kebabName}.blade.php");
        File::put($livewirePath, $livewireContent);

        // Index view
        $indexContent = <<<BLADE
@extends('layouts.app')

@section('content')
    <flux:prose>
        <flux:heading>{$pascalName}</flux:heading>
        <flux:text>Welcome to the {$kebabName} module!</flux:text>
        
        <flux:button href="{{ route('{$kebabName}.module') }}">
            <flux:icon.arrow-right class="w-4 h-4" />
            View Module
        </flux:button>
    </flux:prose>
@endsection
BLADE;

        $indexPath = base_path("modules/" . Str::kebab($name) . "/resources/views/index.blade.php");
        File::put($indexPath, $indexContent);

        // Module view
        $moduleContent = <<<BLADE
@extends('layouts.app')

@section('content')
    <flux:prose>
        <livewire:{{ \$component }} />
    </flux:prose>
@endsection
BLADE;

        $modulePath = base_path("modules/" . Str::kebab($name) . "/resources/views/module.blade.php");
        File::put($modulePath, $moduleContent);
    }

    protected function createRoutes($name)
    {
        $kebabName = Str::kebab($name);
        $pascalName = Str::studly($name);
        
        $content = <<<PHP
<?php

use Illuminate\\Support\\Facades\\Route;
use Ladbu\\LaravelLadwire{$pascalName}\\Http\\Livewire\\{$pascalName};

Route::middleware(config('laravel-ladwire-{$kebabName}.middleware', ['web', 'auth']))
    ->prefix(config('laravel-ladwire-{$kebabName}.route_prefix', '{$kebabName}'))
    ->group(function () {
        Route::get('/', function () {
            return view('laravel-ladwire-{$kebabName}::index');
        })->name('{$kebabName}.index');
        
        Route::get('/module', function () {
            return view('laravel-ladwire-{$kebabName}::module', [
                'component' => 'laravel-ladwire-{$kebabName}::{$kebabName}'
            ]);
        })->name('{$kebabName}.module');
    });
PHP;

        $path = base_path("modules/" . Str::kebab($name) . "/routes/web.php");
        File::put($path, $content);
    }

    protected function createTests($name)
    {
        $kebabName = Str::kebab($name);
        $pascalName = Str::studly($name);
        
        $content = <<<PHP
<?php

namespace Ladbu\\LaravelLadwire{$pascalName}\\Tests\\Feature;

use Ladbu\\LaravelLadwire{$pascalName}\\Tests\\TestCase;
use Livewire\\Livewire;

class {$pascalName}Test extends TestCase
{
    /** @test */
    public function it_can_render_the_component()
    {
        Livewire::test('laravel-ladwire-{$kebabName}::{$kebabName}')
            ->assertSee('{$pascalName}')
            ->assertSee('This is the {$kebabName} module');
    }

    /** @test */
    public function it_can_refresh_data()
    {
        Livewire::test('laravel-ladwire-{$kebabName}::{$kebabName}')
            ->call('refreshData')
            ->assertDispatched('data-refreshed');
    }

    /** @test */
    public function it_displays_stats()
    {
        Livewire::test('laravel-ladwire-{$kebabName}::{$kebabName}')
            ->assertSee('Total Items')
            ->assertSee('Active Items')
            ->assertSee('New Today');
    }
}
PHP;

        $path = base_path("modules/" . Str::kebab($name) . "/tests/Feature/{$pascalName}Test.php");
        File::put($path, $content);
    }

    protected function updateMainPackage($name)
    {
        $kebabName = Str::kebab($name);
        $pascalName = Str::studly($name);
        
        // Update main service provider
        $providerPath = base_path("src/LaravelLadwireModuleServiceProvider.php");
        $providerContent = File::get($providerPath);
        
        $newProvider = "        '{$kebabName}' => 'Ladbu\\\\LaravelLadwire{$pascalName}\\\\{$pascalName}ServiceProvider',";
        
        if (!str_contains($providerContent, $newProvider)) {
            $providerContent = str_replace(
                "        'settings' => 'Ladbu\\\\LaravelLadwireSettings\\\\SettingsServiceProvider',",
                "        'settings' => 'Ladbu\\\\LaravelLadwireSettings\\\\SettingsServiceProvider',\n        {$newProvider}",
                $providerContent
            );
            File::put($providerPath, $providerContent);
        }
        
        // Update installer command
        $installerPath = base_path("src/Console/Commands/InstallLadwireModule.php");
        $installerContent = File::get($installerPath);
        
        // Update signature
        if (!str_contains($installerContent, "{--{$kebabName}}")) {
            $installerContent = str_replace(
                "protected \$signature = 'ladwire:install {--dashboard} {--user-management} {--settings}';",
                "protected \$signature = 'ladwire:install {--dashboard} {--user-management} {--settings} {--{$kebabName}}';",
                $installerContent
            );
        }
        
        // Add install method
        $installMethod = <<<PHP
    protected function install{$pascalName}()
    {
        \$this->info('Installing {$pascalName} module...');
        
        \$this->createController('{$pascalName}', '{$kebabName}');
        \$this->addRoute('{$kebabName}', '{$pascalName}Controller');
        \$this->createView('{$kebabName}');
        
        \$this->info('✅ {$pascalName} module installed');
    }
PHP;

        if (!str_contains($installerContent, "install{$pascalName}()")) {
            $installerContent = str_replace(
                "    protected function installSettings()",
                $installMethod . "\n\n    protected function installSettings()",
                $installerContent
            );
        }
        
        // Update handle method
        if (!str_contains($installerContent, "\$this->option('{$kebabName}')")) {
            $installerContent = str_replace(
                "if (\$this->option('settings')) {\n            \$this->installSettings();\n            \$modules[] = 'settings';\n        }",
                "if (\$this->option('settings')) {\n            \$this->installSettings();\n            \$modules[] = 'settings';\n        }\n        \n        if (\$this->option('{$kebabName}')) {\n            \$this->install{$pascalName}();\n            \$modules[] = '{$kebabName}';\n        }",
                $installerContent
            );
        }
        
        // Update installAllModules
        if (!str_contains($installerContent, "\$this->install{$pascalName}();")) {
            $installerContent = str_replace(
                "\$this->installSettings();\n        \n        \$this->info('✅ All modules installed');",
                "\$this->installSettings();\n        \$this->install{$pascalName}();\n        \n        \$this->info('✅ All modules installed');",
                $installerContent
            );
        }
        
        // Update getViewStub
        $newComponent = "        '{$kebabName}' => 'laravel-ladwire-{$kebabName}::{$kebabName}',";
        if (!str_contains($installerContent, $newComponent)) {
            $installerContent = str_replace(
                "        'settings' => 'laravel-ladwire-settings::settings',",
                "        'settings' => 'laravel-ladwire-settings::settings',\n        {$newComponent}",
                $installerContent
            );
        }
        
        File::put($installerPath, $installerContent);
    }
}
