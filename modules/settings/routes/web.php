<?php

use Illuminate\Support\Facades\Route;
use Ladbu\LaravelLivewireSettings\Http\Livewire\Settings;

Route::middleware(['web'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/settings', Settings::class)->name('settings');
    });
});

Route::middleware(['web'])->group(function () {
    Route::get('/module-settings', Settings::class)->name('module.settings');
});
