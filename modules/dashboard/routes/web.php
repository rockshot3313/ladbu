<?php

use Illuminate\Support\Facades\Route;
use Ladbu\LaravelLadwireDashboard\Http\Controllers\DashboardController;
use Ladbu\LaravelLadwireDashboard\Http\Livewire\Dashboard;

Route::middleware(['web', 'auth', 'verified'])->group(function () {
    // Main dashboard route following starter kit pattern
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('/dashboard', DashboardController::class)->name('dashboard.controller');
});

Route::middleware(['web'])->group(function () {
    // Ladwire module routes
    Route::get('/module-dashboard', Dashboard::class)->name('module.dashboard');
    
    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', Dashboard::class)->name('dashboard');
    });
});
