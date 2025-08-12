<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\RakController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;

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

Route::middleware(['auth'])->group(function () {
    Route::get('/items', [ItemController::class, 'index'])->name('items.index');
    
    // Hanya admin & superadmin bisa CRUD
    Route::middleware('role:admin,superadmin')->group(function () {
        Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
        Route::post('/items', [ItemController::class, 'store'])->name('items.store');
        Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
        Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
        Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
    });
    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/daftar-barang', [BarangController::class, 'index'])->name('daftar-barang');
    Route::get('/tatanan-rak', [RakController::class, 'index'])->name('tatanan-rak');

    Route::middleware(['role:superadmin'])->group(function () {
        Route::get('/aktifitas-log', [LogController::class, 'index'])->name('aktifitas-log');
        Route::get('/kelola-akun', [UserController::class, 'manage'])->name('kelola-akun');
    });
});


require __DIR__.'/auth.php';
