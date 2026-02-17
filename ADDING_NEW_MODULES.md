# Adding New Modules to Laravel Ladwire Module

This guide walks you through creating a new module step by step.

## Step 1: Create Module Directory Structure

Create a new directory in `modules/`:

```
modules/
├── dashboard/
├── user-management/
├── settings/
└── your-new-module/          # ← Create this
    ├── composer.json
    ├── config/
    ├── resources/
    ├── routes/
    └── src/
```

## Step 2: Create Module composer.json

Create `modules/your-new-module/composer.json`:

```json
{
    "name": "ladbu/laravel-ladwire-your-new-module",
    "description": "Your new module for Laravel Ladwire",
    "type": "laravel-package",
    "keywords": ["laravel", "ladwire", "your-new-module"],
    "license": "MIT",
    "authors": [
        {
            "name": "Ladbu",
            "email": "geraldalbacite.org@gmail.com"
        }
    ],
    "require": {
        "php": "^8.2",
        "illuminate/support": "^11.0|^12.0",
        "livewire/livewire": "^3.0|^4.0",
        "livewire/flux": "^1.0|^2.0"
    },
    "require-dev": {
        "orchestra/testbench": "^7.0|^8.0",
        "phpunit/phpunit": "^9.0|^10.0"
    },
    "autoload": {
        "psr-4": {
            "Ladbu\\LaravelLadwireYourNewModule\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Ladbu\\LaravelLadwireYourNewModule\\Tests\\": "tests/"
        }
    },
    "extra": {
        "laravel": {
            "providers": [
                "Ladbu\\LaravelLadwireYourNewModule\\YourNewModuleServiceProvider"
            ]
        }
    },
    "minimum-stability": "stable",
    "prefer-stable": true
}
```

## Step 3: Create Module Service Provider

Create `modules/your-new-module/src/YourNewModuleServiceProvider.php`:

```php
<?php

namespace Ladbu\LaravelLadwireYourNewModule;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

class YourNewModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/your-new-module.php',
            'laravel-ladwire-your-new-module'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-ladwire-your-new-module');
        
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/your-new-module.php' => config_path('laravel-ladwire-your-new-module.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-ladwire-your-new-module'),
            ], 'views');
        }

        $this->registerLadwireComponents();
    }

    /**
     * Register Ladwire components.
     */
    protected function registerLadwireComponents(): void
    {
        Livewire::component('laravel-ladwire-your-new-module::your-new-module', \Ladbu\LaravelLadwireYourNewModule\Http\Livewire\YourNewModule::class);
    }
}
```

## Step 4: Create Module Configuration

Create `modules/your-new-module/config/your-new-module.php`:

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Module Enabled
    |--------------------------------------------------------------------------
    |
    | This option controls whether the Your New Module is enabled.
    | When set to false, the module's routes and components will not be
    | registered.
    |
    */
    'enabled' => env('LARAVEL_LADWIRE_YOUR_NEW_MODULE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | This option controls the route prefix for the module.
    | Default is 'your-new-module'.
    |
    */
    'route_prefix' => env('LARAVEL_LADWIRE_YOUR_NEW_MODULE_ROUTE_PREFIX', 'your-new-module'),

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
```

## Step 5: Create Livewire Component

Create `modules/your-new-module/src/Http/Livewire/YourNewModule.php`:

```php
<?php

namespace Ladbu\LaravelLadwireYourNewModule\Http\Livewire;

use Livewire\Component;

class YourNewModule extends Component
{
    public $data = [];
    public $loading = false;

    public function mount()
    {
        $this->data = [
            'title' => 'Your New Module',
            'description' => 'This is your new module description',
            'stats' => [
                'total_items' => 100,
                'active_items' => 45,
                'new_items_today' => 12,
            ]
        ];
    }

    public function refreshData()
    {
        $this->loading = true;
        
        // Simulate API call or data processing
        sleep(1);
        
        $this->data['stats']['total_items'] = rand(90, 110);
        $this->data['stats']['active_items'] = rand(40, 50);
        $this->data['stats']['new_items_today'] = rand(10, 15);
        
        $this->loading = false;
        
        $this->dispatch('data-refreshed');
    }

    public function render()
    {
        return view('laravel-ladwire-your-new-module::livewire.your-new-module');
    }
}
```

## Step 6: Create Blade View

Create `modules/your-new-module/resources/views/livewire/your-new-module.blade.php`:

```blade
<flux:heading>{{ $data['title'] }}</flux:heading>

<flux:prose>
    <flux:text>{{ $data['description'] }}</flux:text>
</flux:prose>

<!-- Stats Grid -->
<flux:grid class="mb-8">
    <flux:card>
        <flux:card.content>
            <div class="flex items-center">
                <flux:icon.chart-bar class="w-6 h-6 text-blue-600" />
                <div class="ml-4">
                    <flux:text size="sm" color="gray">Total Items</flux:text>
                    <flux:text size="2xl" weight="semibold">{{ $data['stats']['total_items'] }}</flux:text>
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
                    <flux:text size="2xl" weight="semibold">{{ $data['stats']['active_items'] }}</flux:text>
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
                    <flux:text size="2xl" weight="semibold">{{ $data['stats']['new_items_today'] }}</flux:text>
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
            {{ $loading ? 'Refreshing...' : 'Refresh Data' }}
        </flux:button>
    </flux:card.content>
</flux:card>

<!-- Notifications -->
@if (session()->has('success'))
    <flux:toast variant="success">
        {{ session('success') }}
    </flux:toast>
@endif
```

## Step 7: Create Routes

Create `modules/your-new-module/routes/web.php`:

```php
<?php

use Illuminate\Support\Facades\Route;
use Ladbu\LaravelLadwireYourNewModule\Http\Livewire\YourNewModule;

Route::middleware(config('laravel-ladwire-your-new-module.middleware', ['web', 'auth']))
    ->prefix(config('laravel-ladwire-your-new-module.route_prefix', 'your-new-module'))
    ->group(function () {
        Route::get('/', function () {
            return view('laravel-ladwire-your-new-module::index');
        })->name('your-new-module.index');
        
        Route::get('/module', function () {
            return view('laravel-ladwire-your-new-module::module', [
                'component' => 'laravel-ladwire-your-new-module::your-new-module'
            ]);
        })->name('your-new-module.module');
    });
```

## Step 8: Create Index View

Create `modules/your-new-module/resources/views/index.blade.php`:

```blade
@extends('layouts.app')

@section('content')
    <flux:prose>
        <flux:heading>Your New Module</flux:heading>
        <flux:text>Welcome to your new module!</flux:text>
        
        <flux:button href="{{ route('your-new-module.module') }}">
            <flux:icon.arrow-right class="w-4 h-4" />
            View Module
        </flux:button>
    </flux:prose>
@endsection
```

## Step 9: Create Module View

Create `modules/your-new-module/resources/views/module.blade.php`:

```blade
@extends('layouts.app')

@section('content')
    <flux:prose>
        <livewire:{{ $component }} />
    </flux:prose>
@endsection
```

## Step 10: Update Main Package

### Update Main Service Provider

Add your new module to `src/LaravelLadwireModuleServiceProvider.php`:

```php
protected function discoverModules(): array
{
    $modules = [];
    
    // Check for installed modules
    $modulePackages = [
        'dashboard' => 'Ladbu\\LaravelLadwireDashboard\\DashboardServiceProvider',
        'user-management' => 'Ladbu\\LaravelLadwireUserManagement\\UserManagementServiceProvider',
        'settings' => 'Ladbu\\LaravelLadwireSettings\\SettingsServiceProvider',
        'your-new-module' => 'Ladbu\\LaravelLadwireYourNewModule\\YourNewModuleServiceProvider', // ← Add this
    ];

    foreach ($modulePackages as $module => $provider) {
        if (class_exists($provider)) {
            $modules[$module] = [
                'name' => $module,
                'provider' => $provider,
                'enabled' => true,
            ];
        }
    }

    return $modules;
}
```

### Update Installer Command

Add your new module to `src/Console/Commands/InstallLadwireModule.php`:

```php
protected $signature = 'ladwire:install {--dashboard} {--user-management} {--settings} {--your-new-module}';

// In handle() method:
if ($this->option('your-new-module')) {
    $this->installYourNewModule();
    $modules[] = 'your-new-module';
}

// Add method:
protected function installYourNewModule()
{
    $this->info('Installing Your New Module...');
    
    $this->createController('YourNewModule', 'your-new-module');
    $this->addRoute('your-new-module', 'YourNewModuleController');
    $this->createView('your-new-module');
    
    $this->info('✅ Your New Module installed');
}

// Update installAllModules():
protected function installAllModules()
{
    $this->info('Installing all Ladwire modules...');
    
    $this->installDashboard();
    $this->installUserManagement();
    $this->installSettings();
    $this->installYourNewModule(); // ← Add this
    
    $this->info('✅ All modules installed');
}

// Update getViewStub():
protected function getViewStub($name)
{
    $componentMap = [
        'dashboard' => 'laravel-ladwire-dashboard::dashboard',
        'user-management' => 'laravel-ladwire-user-management::user-management',
        'settings' => 'laravel-ladwire-settings::settings',
        'your-new-module' => 'laravel-ladwire-your-new-module::your-new-module', // ← Add this
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
```

### Update composer.json

Add your new module to suggestions:

```json
"suggest": {
    "ladbu/laravel-ladwire-dashboard": "Install dashboard module for statistics and overview",
    "ladbu/laravel-ladwire-user-management": "Install user management module for CRUD operations",
    "ladbu/laravel-ladwire-settings": "Install settings module for configuration management",
    "ladbu/laravel-ladwire-your-new-module": "Install your new module for additional functionality"
}
```

## Step 11: Create Tests

Create `modules/your-new-module/tests/Feature/YourNewModuleTest.php`:

```php
<?php

namespace Ladbu\LaravelLadwireYourNewModule\Tests\Feature;

use Ladbu\LaravelLadwireYourNewModule\Tests\TestCase;
use Livewire\Livewire;

class YourNewModuleTest extends TestCase
{
    /** @test */
    public function it_can_render_the_component()
    {
        Livewire::test('laravel-ladwire-your-new-module::your-new-module')
            ->assertSee('Your New Module')
            ->assertSee('This is your new module description');
    }

    /** @test */
    public function it_can_refresh_data()
    {
        Livewire::test('laravel-ladwire-your-new-module::your-new-module')
            ->call('refreshData')
            ->assertDispatched('data-refreshed');
    }

    /** @test */
    public function it_displays_stats()
    {
        Livewire::test('laravel-ladwire-your-new-module::your-new-module')
            ->assertSee('Total Items')
            ->assertSee('Active Items')
            ->assertSee('New Today');
    }
}
```

## Step 12: Update Documentation

### Update README.md

Add your module to the features list:

```markdown
## Features

- **Modular Architecture**: Install only the modules you need
- **Dashboard**: Statistics and recent activity overview
- **User Management**: CRUD operations for users with search and pagination
- **Settings**: Configurable application settings
- **Your New Module**: [Brief description of what it does]
- **Ladwire Powered**: All components use Ladwire for reactive UI
- **Flux UI**: Modern, accessible component library
- **Easy Integration**: Simple installation and configuration
```

Add module section:

```markdown
### Your New Module

[Brief description of what your module does]

**Package**: `ladbu/laravel-ladwire-your-new-module`

**Features**:
- Feature 1 description
- Feature 2 description
- Feature 3 description
```

### Update Usage Section

Add your module routes:

```markdown
### Your New Module Module
- `/module-your-new-module` - Your new module interface
- `/admin/your-new-module` - Admin your new module
```

### Update Component Usage

Add your component:

```blade
<!-- Your New Module Component -->
<livewire:laravel-ladwire-your-new-module::your-new-module />
```

## Step 13: Publish Your Module

1. **Create separate repository** for your module
2. **Publish to Packagist**:
   ```bash
   git tag v1.0.0
   git push origin v1.0.0
   ```
3. **Submit to Packagist** with your repository URL
4. **Update main package** with new version

## Step 14: Testing Your Module

1. **Install in test project**:
   ```bash
   composer require ladbu/laravel-ladwire-your-new-module
   php artisan ladwire:install --your-new-module
   ```

2. **Test functionality**:
   - Visit `/your-new-module`
   - Test component interactions
   - Verify Flux UI components work

3. **Run tests**:
   ```bash
   php artisan test
   ```

## File Structure Summary

```
modules/your-new-module/
├── composer.json
├── config/
│   └── your-new-module.php
├── resources/
│   └── views/
│       ├── index.blade.php
│       ├── module.blade.php
│       └── livewire/
│           └── your-new-module.blade.php
├── routes/
│   └── web.php
└── src/
    ├── Http/
    │   └── Livewire/
    │       └── YourNewModule.php
    └── YourNewModuleServiceProvider.php
```

## Best Practices

1. **Follow naming conventions** (kebab-case for routes, PascalCase for classes)
2. **Use Flux UI components** consistently
3. **Add proper error handling** in Livewire components
4. **Include comprehensive tests** for all functionality
5. **Document all features** in README
6. **Use environment variables** for configuration
7. **Follow Laravel coding standards**
8. **Add proper validation** for user inputs
9. **Include loading states** for async operations
10. **Add accessibility features** using Flux UI

## Next Steps

After creating your module:

1. **Test thoroughly** in different Laravel versions
2. **Add more features** based on user feedback
3. **Create documentation** with examples
4. **Publish to Packagist** as separate package
5. **Update main package** to include your module
6. **Add to Laravel Livewire Starter Kit** if applicable

Your new module is now ready to be part of the Laravel Ladwire Module ecosystem! 🎉
