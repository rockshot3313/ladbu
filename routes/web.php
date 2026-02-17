<?php

use Illuminate\Support\Facades\Route;
use Ladbu\LaravelLivewireModule\Http\Livewire\Dashboard;
use Ladbu\LaravelLivewireModule\Http\Livewire\UserManagement;
use Ladbu\LaravelLivewireModule\Http\Livewire\Settings;

Route::middleware(['web'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', Dashboard::class)->name('dashboard');
        Route::get('/users', UserManagement::class)->name('users');
        Route::get('/settings', Settings::class)->name('settings');
    });
});

Route::middleware(['web'])->group(function () {
    Route::get('/module-dashboard', Dashboard::class)->name('module.dashboard');
    Route::get('/module-users', UserManagement::class)->name('module.users');
    Route::get('/module-settings', Settings::class)->name('module.settings');
});
