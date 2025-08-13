<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\RakController;
use App\Http\Controllers\KelolaAkunController;
use Illuminate\Support\Facades\Route;
use App\Livewire\ManageUsers;


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

    Route::get('/dashboard', function () {
        return view('layouts.dashboard'); // layouts/dashboard.blade.php
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware(['auth'])->group(function () {
        // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/tatanan-rak', [RakController::class, 'index'])->name('tatanan-rak');

    Route::middleware(['role:superadmin'])->group(function () {
        Route::get('/aktifitas-log', [LogController::class, 'index'])->name('aktifitas-log');
        Route::get('/kelola-akun', [UserController::class, 'manage'])->name('kelola-akun');
    });
});



    Route::middleware(['auth', 'superadmin'])->group(function () {
        Route::get('/kelola-akun', ManageUsers::class)->name('kelola-akun');
    });


    Route::middleware(['auth', 'can:superadmin-only'])->group(function () {
        Route::get('/kelola-akun/tambah', [RegisteredUserController::class, 'create'])
            ->name('kelola-akun.create');

        Route::post('/kelola-akun/tambah', [RegisteredUserController::class, 'store'])
            ->name('kelola-akun.store');
    });

    Route::get('/kelola-akun/{id}/edit', [KelolaAkunController::class, 'edit'])->name('kelola-akun.edit');
    Route::put('/kelola-akun/{id}', [KelolaAkunController::class, 'update'])->name('kelola-akun.update');

    Route::get('/manage-users', \App\Livewire\ManageUsers::class)->name('livewire.manage-users');


    Route::get('/aktifitas-log', [App\Http\Controllers\LogController::class, 'index'])
    ->name('aktifitas-log');


        Route::middleware(['auth', 'can:superadmin-only'])->group(function () {
        Route::get('/log-aktivitas', [LogController::class, 'index'])->name('log-aktivitas');
    });

        


require __DIR__.'/auth.php';
