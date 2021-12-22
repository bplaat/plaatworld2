<?php

use Illuminate\Support\Facades\Route;

// Main routes
Route::view('/', 'home')->name('home');

// Normal routes
Route::middleware('auth')->group(function () {
    Route::view('/settings', 'settings')->name('settings');

    Route::get('/auth/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('auth.logout');
});

// Admin routes
Route::middleware('admin')->group(function () {
    Route::view('/admin', 'admin.home')->name('admin.home');

    Route::get('/admin/users', App\Http\Livewire\Admin\Users\Crud::class)->name('admin.users.crud');

    Route::get('/admin/worlds', App\Http\Livewire\Admin\Worlds\Crud::class)->name('admin.worlds.crud');

    Route::get('/admin/textures', App\Http\Livewire\Admin\Textures\Crud::class)->name('admin.textures.crud');

    Route::get('/admin/objects', App\Http\Livewire\Admin\Objects\Crud::class)->name('admin.objects.crud');
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/auth/login', App\Http\Livewire\Auth\Login::class)->name('auth.login');

    Route::get('/auth/register', App\Http\Livewire\Auth\Register::class)->name('auth.register');
});
