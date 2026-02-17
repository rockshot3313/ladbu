# Laravel Ladwire Module with Flux UI

A modular Laravel package that adds Ladwire components to fresh Laravel projects with dashboard, user management, and settings functionality, built with modern Flux UI components.

## Features

- **Modular Architecture**: Install only the modules you need
- **Dashboard**: Statistics and recent activity overview
- **User Management**: CRUD operations for users with search and pagination
- **Settings**: Configurable application settings
- **Ladwire Powered**: All components use Ladwire for reactive UI
- **Flux UI**: Modern, accessible component library
- **Easy Integration**: Simple installation and configuration

## Requirements

- PHP 8.2+
- Laravel 11.0+ / 12.0+
- Livewire 3.0+
- Flux UI 1.0+

## Installation

### For Package Development

If you're developing this package or want to use it in an existing project:

```bash
composer require ladbu/laravel-ladwire-module
```

### For Fresh Laravel Projects

For fresh Laravel projects, you can use the installer command to set up everything quickly:

```bash
# Install the package in your fresh project
composer require ladbu/laravel-ladwire-module

# Install specific modules
php artisan ladwire:install --dashboard
php artisan ladwire:install --user-management
php artisan ladwire:install --settings

# Install all modules
php artisan ladwire:install
```

### Post-Installation Steps

After running the installer command:

1. **Install and configure Flux UI** (if not already installed):

```bash
php artisan flux:install
```

2. **Publish views** (optional, if you want to customize them):

```bash
php artisan vendor:publish --tag="ladwire-views" --provider="Ladbu\\LaravelLadwireModule\\LaravelLadwireModuleServiceProvider"
```

### For Fresh Projects - Quick Start

The installer command creates:
- **Controllers** in `app/Http/Controllers/`
- **Routes** in `routes/web.php`
- **Views** in `resources/views/ladwire/`

Example usage in your fresh project:

```php
// routes/web.php
Route::get('/dashboard', DashboardController::class)->name('dashboard');
Route::get('/users', UserManagementController::class)->name('users');
Route::get('/settings', SettingsController::class)->name('settings');
```

```blade
{{-- resources/views/ladwire/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
    <flux:prose>
        <livewire:laravel-ladwire-dashboard::dashboard />
    </flux:prose>
@endsection
```

## Usage

Once installed, you can access the modules at the following routes:

### Dashboard Module
- `/module-dashboard` - Dashboard overview
- `/admin/dashboard` - Admin dashboard

### User Management Module
- `/module-users` - User management interface
- `/admin/users` - Admin user management

### Settings Module
- `/module-settings` - Application settings
- `/admin/settings` - Admin settings

### Using Components in Your Views

You can also use the individual Livewire components in your own views:

```blade
<!-- Dashboard Component -->
<livewire:laravel-ladwire-dashboard::dashboard />

<!-- User Management Component -->
<livewire:laravel-ladwire-user-management::user-management />

<!-- Settings Component -->
<livewire:laravel-ladwire-settings::settings />
```

### Module Discovery

The main package automatically discovers and registers any installed modules. If you install a module separately, it will be automatically available without additional configuration.

### Ladwire Components

The package uses Ladwire with Flux UI components for a modern, accessible interface. Some key components used:

- `<flux:card>` - Container components
- `<flux:table>` - Data tables with built-in responsiveness
- `<flux:modal>` - Modal dialogs
- `<flux:form>` - Form handling with validation
- `<flux:button>` - Button components with variants
- `<flux:icon>` - Icon components
- `<flux:badge>` - Status indicators
- `<flux:avatar>` - User avatars
- `<flux:checkbox>` - Toggle switches
- `<flux:dropdown>` - Dropdown menus
- `<flux:navbar>` - Navigation components

## Modules

### Dashboard Module

Provides a comprehensive dashboard with:
- Statistics cards (users, posts, activity)
- Recent activity timeline
- Responsive grid layout
- Real-time updates with Livewire

**Package**: `ladbu/laravel-ladwire-dashboard`

### User Management Module

Complete user administration with:
- User listing with search and pagination
- Create, edit, and delete users
- Role management
- Bulk operations support
- Modal-based forms

**Package**: `ladbu/laravel-ladwire-user-management`

### Settings Module

Application configuration management:
- General settings (site name, description)
- Email configuration
- Feature toggles
- Form validation
- Settings persistence

**Package**: `ladbu/laravel-ladwire-settings`

## Testing

### Running Tests

The package includes a comprehensive test suite to ensure all modules work correctly:

```bash
# Run all tests
composer test

# Run specific test suites
composer test --testsuite=Feature
composer test --testsuite=Unit

# Run with coverage
composer test --coverage-html
```

### Test Structure

``bash
tests/
├── Feature/
│   ├── DashboardTest.php
│   ├── UserManagementTest.php
│   └── SettingsTest.php
├── Unit/
├── CreatesApplication.php
├── TestCase.php
└── laravel-app-example/          # Example Laravel app for integration testing
    ├── .env.example
    ├── composer.json
    ├── database/
    │   └── migrations/
    ├── app/Models/
    ├── resources/views/ladwire/
    └── routes/web.php
```

### Integration Testing

For comprehensive testing, use the included example Laravel app:

```bash
# Copy the example app
cp -r tests/laravel-app-example/* .

# Install dependencies
cd tests/laravel-app-example && composer install

# Run tests
composer test
```

### Test Coverage

The package is configured for test coverage reporting. Coverage reports will be generated in:
- `build/logs/clover.xml` (for CI/CD)
- `build/logs/coverage.html` (for visual inspection)

### Continuous Integration

Example GitHub Actions workflow:

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    - uses: shivammathur/composer-dependency-action@v1
    - name: Install Dependencies
      run: composer install --prefer-dist --no-progress --no-suggest --optimize-autoloader --no-scripts
    
    - name: Run Tests
      run: vendor/bin/phpunit --coverage-clover --coverage-html
```

## Requirements

- PHP 8.2+
- Laravel 11.0+ / 12.0+
- Livewire 3.0+
- Flux UI 1.0+

### Environment Variables

You can configure modules using environment variables:

```env
# Enable/disable individual modules
LARAVEL_LADWIRE_DASHBOARD_ENABLED=true
LARAVEL_LADWIRE_USER_MANAGEMENT_ENABLED=true
LARAVEL_LADWIRE_SETTINGS_ENABLED=true

# Custom route prefixes
LARAVEL_LADWIRE_DASHBOARD_ROUTE_PREFIX=admin
LARAVEL_LADWIRE_USER_MANAGEMENT_ROUTE_PREFIX=admin
LARAVEL_LADWIRE_SETTINGS_ROUTE_PREFIX=admin
```

## Customization

### Overriding Views

Publish the views to customize them:

```bash
php artisan vendor:publish --tag="views" --provider="Ladbu\\LaravelLadwireModule\\LaravelLadwireModuleServiceProvider"
```

The views will be published to `resources/views/vendor/laravel-livewire-module/`.

### Extending Components

You can extend the package components in your application:

```php
<?php

namespace App\Http\Livewire;

use Ladbu\LaravelLivewireModule\Http\Livewire\Dashboard as BaseDashboard;

class CustomDashboard extends BaseDashboard
{
    // Override methods or add new functionality
}
```

## Development

### Installation for Development

1. Clone this repository
2. Install dependencies:

```bash
composer install
```

3. Run tests:

```bash
composer test
```

### Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Support

If you encounter any issues or have questions, please open an issue on the GitHub repository.

## Changelog

### v1.0.0
- Initial release
- **NEW**: Modular architecture allowing selective installation
- Dashboard component with statistics
- User management with CRUD operations
- Settings panel with configuration options
- Flux UI integration for modern, accessible components
- Ladwire integration
- Auto-discovery of installed modules

## Roadmap

- [ ] File management module
- [ ] Analytics module
- [ ] Notification system module
- [ ] Blog module
- [ ] E-commerce module
- [ ] API documentation module
