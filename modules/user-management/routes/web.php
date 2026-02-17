<?php

use Illuminate\Support\Facades\Route;
use Ladbu\LaravelLivewireUserManagement\Http\Livewire\UserManagement;

Route::middleware(['web'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', UserManagement::class)->name('users');
    });
});

Route::middleware(['web'])->group(function () {
    Route::get('/module-users', UserManagement::class)->name('module.users');
});
