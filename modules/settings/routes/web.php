<?php

use Illuminate\Support\Facades\Route;
use Ladbu\LaravelLadwireSettings\Http\Livewire\SettingsPage;

Route::middleware(['web'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/settings', SettingsPage::class)->name('settings');
    });
});

Route::middleware(['web'])->group(function () {
    Route::get('/module-settings', SettingsPage::class)->name('module.settings');
});
