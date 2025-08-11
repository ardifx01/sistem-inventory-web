<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route::get('/dashboard/superadmin', fn() => view('dashboard.superadmin'))
//     ->middleware(['auth', 'role:superadmin'])
//     ->name('dashboard.superadmin');

// Route::get('/dashboard/admin', fn() => view('dashboard.admin'))
//     ->middleware(['auth', 'role:admin'])
//     ->name('dashboard.admin');

// Route::get('/dashboard/user', fn() => view('dashboard.user'))
//     ->middleware(['auth', 'role:user'])
//     ->name('dashboard.user');

Route::get('/dashboard', function () {
    return view('layouts.dashboard'); // layouts/dashboard.blade.php
})->middleware(['auth', 'verified'])->name('dashboard');



require __DIR__.'/auth.php';
