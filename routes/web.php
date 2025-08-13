<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\RackController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KelolaAkunController;
use App\Livewire\ManageUsers;
use Illuminate\Support\Facades\Route;

// -------------------------
// Halaman Utama
// -------------------------
Route::get('/', function () {
    return view('welcome');
});

// -------------------------
// Dashboard
// -------------------------
Route::get('/dashboard', function () {
    return view('layouts.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// -------------------------
// Profil
// -------------------------
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// -------------------------
// Barang (Items)
// -------------------------
Route::middleware('auth')->group(function () {
    Route::get('/items', [ItemController::class, 'index'])->name('items.index');

    // CRUD khusus admin & superadmin
    Route::middleware('role:admin,superadmin')->group(function () {
        Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
        Route::post('/items', [ItemController::class, 'store'])->name('items.store');
        Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
        Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
        Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
    });
});

// -------------------------
// Khusus Superadmin
// -------------------------
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
// -------------------------
// Menu Umum
// -------------------------
Route::get('/daftar-barang', [BarangController::class, 'index'])->name('daftar-barang');
Route::get('/tatanan-rack', [RackController::class, 'index'])->name('tatanan-rack');

// -------------------------
// Auth Routes
// -------------------------
require __DIR__.'/auth.php';
