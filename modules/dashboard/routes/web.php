<?php

use Illuminate\Support\Facades\Route;
use Ladbu\LaravelLadwireDashboard\Http\Controllers\DashboardController;
use Ladbu\LaravelLadwireDashboard\Http\Livewire\DashboardPage;

Route::middleware(['web', 'auth', 'verified'])->group(function () {
    // Main dashboard route following starter kit pattern
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('/dashboard', DashboardController::class)->name('dashboard.controller');
});

Route::middleware(['web'])->group(function () {
    // Ladwire module routes
    Route::get('/module-dashboard', DashboardPage::class)->name('module.dashboard');
    
    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', DashboardPage::class)->name('dashboard');
    });
});
