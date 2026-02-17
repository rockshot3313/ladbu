<?php

use Illuminate\Support\Facades\Route;
use Ladbu\LaravelLivewireDashboard\Http\Livewire\Dashboard;

Route::middleware(['web'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', Dashboard::class)->name('dashboard');
    });
});

Route::middleware(['web'])->group(function () {
    Route::get('/module-dashboard', Dashboard::class)->name('module.dashboard');
});
