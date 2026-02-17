# Testing Ladwire Modules in Fresh Projects

## Quick Testing Guide

Since the Ladwire package is designed for fresh Laravel projects, here's how to test the modules:

### Option 1: Create a Fresh Laravel Project

```bash
# Create new Laravel project
laravel new ladwire-test-app

# Navigate to project
cd ladwire-test-app

# Install Ladwire package
composer require ladbu/laravel-ladwire-module

# Install Flux UI
php artisan flux:install

# Publish views (optional)
php artisan vendor:publish --tag="views" --provider="Ladbu\\LaravelLadwireModule\\LaravelLadwireModuleServiceProvider"

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed
```

### Option 2: Use Existing Laravel Project

If you have an existing Laravel project:

```bash
# Install Ladwire package
composer require ladbu/laravel-ladwire-module

# Install Flux UI (if not already installed)
php artisan flux:install

# Publish views
php artisan vendor:publish --tag="views" --provider="Ladbu\\LaravelLadwireModule\\LaravelLadwireModuleServiceProvider"

# Run migrations (if needed)
php artisan migrate

# Add routes to web.php (if not auto-added)
# Add the Ladwire routes manually
```

### Testing Individual Modules

```bash
# Test specific module
php artisan test --filter=DashboardTest

# Run all tests
php artisan test
```

### What Gets Tested

✅ **Dashboard Module**:
- Statistics display
- Recent activity timeline
- Component rendering
- Data handling

✅ **User Management Module**:
- User listing with pagination
- Search functionality
- Create/Edit/Delete operations
- Form validation
- Modal interactions

✅ **Settings Module**:
- Configuration form display
- Settings persistence
- Toggle switches
- Form validation
- Reset functionality

### Manual Testing

You can also test manually:

1. **Visit Routes**:
   - `/module-dashboard` - Dashboard module
   - `/module-users` - User management
   - `/module-settings` - Settings module
   - `/admin/dashboard` - Admin dashboard
   - `/admin/users` - Admin user management
   - `/admin/settings` - Admin settings

2. **Check Components**:
   - All Livewire components should render
   - JavaScript events should fire
   - Forms should validate properly

3. **Verify Installation**:
   - Controllers should be created in `app/Http/Controllers/`
   - Routes should be added to `routes/web.php`
   - Views should be published correctly

### Test Coverage

Run tests with coverage:
```bash
composer test
```

Or with coverage:
```bash
composer test --coverage-html
```

Coverage reports will be generated in `build/logs/coverage.html`

### Troubleshooting

If tests fail:
1. Check Flux UI is installed: `php artisan flux:install`
2. Verify routes are registered: `php artisan route:list`
3. Check Livewire components: `php artisan livewire:discover`
4. Clear caches: `php artisan config:clear && php artisan view:clear`

The package includes comprehensive test coverage for all modules to ensure reliability!
