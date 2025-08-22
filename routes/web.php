<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\RackController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ResetPasswordController;
use Illuminate\Support\Facades\Route;



// -------------------------
// Halaman Utama
// -------------------------
Route::get('/', function () {
    return view('auth.login');
});

// -------------------------
// Dashboard
// -------------------------

Route::middleware(['auth', 'verified'])->group(function(){
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/barang-baru', [DashboardController::class, 'getBarangBaru']);
});

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
        Route::delete('/items/bulk-delete', [ItemController::class, 'bulkDelete'])->name('items.bulkDelete');
        Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
    });
});

// -------------------------
// Kelola Akun (Superadmin Only)
// -------------------------
Route::get('/request-reset', [UserController::class, 'showRequestForm'])->name('request-reset.form');
Route::post('/request-reset', [UserController::class, 'requestReset'])->name('request-reset.submit');

// -------------------------
// Form Forgot Password
// -------------------------
Route::get('/request-reset', [UserController::class, 'showRequestForm'])
    ->name('request-reset.form'); // form bisa diakses semua user

// -------------------------
// Proses submit request reset
// -------------------------
Route::post('/request-reset', [UserController::class, 'requestReset'])
    ->name('request.reset'); // proses juga bisa diakses semua user

// -------------------------
// Kelola Akun (Superadmin Only)
// -------------------------
Route::middleware(['auth', 'can:superadmin-only'])->group(function () {
    
    // Index / daftar akun
    Route::get('/kelola-akun', [UserController::class, 'index'])->name('kelola-akun');

    // Tambah akun baru
    Route::get('/kelola-akun/tambah', [UserController::class, 'create'])->name('kelola-akun.create');
    Route::post('/kelola-akun/tambah', [UserController::class, 'store'])->name('kelola-akun.store');

    // Edit akun
    Route::get('/kelola-akun/{id}/edit', [UserController::class, 'edit'])->name('kelola-akun.edit');
    Route::put('/kelola-akun/{id}', [UserController::class, 'update'])->name('kelola-akun.update');
    Route::get('/kelola-akun/autocomplete', [UserController::class, 'autocomplete'])->name('kelola-akun.autocomplete');

    // Toggle status aktif/inaktif
    Route::put('/kelola-akun/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('kelola-akun.toggle-status');


    // Endpoint ambil notifikasi (AJAX / fetch JSON)
    Route::get('/kelola-akun/notif', [UserController::class, 'getNotifications'])->name('kelola-akun.notif');

    // Endpoint tandai notifikasi dibaca
    // web.php
Route::post('/notifications/{id}/read', [UserController::class, 'markAsRead'])
    ->name('notifications.read');

});

// -------------------------
// ActivityLogs
// -------------------------


Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/aktifitas-log', [LogController::class, 'index'])->name('aktifitas-log');
    Route::delete('/aktifitas-log/{id}', [LogController::class, 'destroy'])->name('aktifitas-log.destroy');
    Route::delete('/aktifitas-log', [LogController::class, 'clearAll'])->name('aktifitas-log.clear');
    Route::delete('/aktifitas-log/bulk-destroy', [LogController::class, 'bulkDestroy'])->name('aktifitas-log.bulk-destroy');


});
// -------------------------
// Menu Umum
// -------------------------
Route::get('/tatanan-rack', [RackController::class, 'index'])->name('tatanan-rack');




// -------------------------
// Auth Routes
// -------------------------
require __DIR__.'/auth.php';
