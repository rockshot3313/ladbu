<?php

use Illuminate\Support\Facades\Route;
use Ladbu\LaravelLadwireUserManagement\Http\Livewire\UserManagementPage;

Route::middleware(['web'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', UserManagementPage::class)->name('users');
    });
});

Route::middleware(['web'])->group(function () {
    Route::get('/module-users', UserManagementPage::class)->name('module.users');
});
