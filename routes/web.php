<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\RackController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KelolaAkunController;
use Illuminate\Support\Facades\Route;
use Spatie\Activitylog\Facades\LogBatch;
use App\Models\Item;
use App\Models\Category;


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
    // 1. Total Barang
    $totalBarang = Item::count();

    // 2. Total Kategori
    $totalKategori = Category::count();

    // 3. Total Rak (prefix sebelum "-")
    $totalRak = Item::whereNotNull('rack_location')
                    ->where('rack_location', '!=', '')
                    ->where('rack_location', '!=', 'ZIP')
                    ->selectRaw("DISTINCT SUBSTRING_INDEX(rack_location, '-', 1) as rak_prefix")
                    ->get()
                    ->count();

    // 4. Barang Belum Masuk Rak
    $belumMasukRak = Item::where('rack_location', 'ZIP')->count();

    // 5. Barang Baru (4 item terakhir, hanya kolom tertentu)
    $barangBaru = Item::select('name', 'item_code', 'rack_location')
                      ->latest()
                      ->take(8)
                      ->get();

    return view('layouts.dashboard', compact(
        'totalBarang',
        'totalKategori',
        'totalRak',
        'belumMasukRak',
        'barangBaru'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/barang-baru', [DashboardController::class, 'getBarangBaru']);


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
Route::middleware(['auth', 'can:superadmin-only'])->group(function () {

    // Index / daftar akun
    Route::get('/kelola-akun', [UserController::class, 'index'])->name('kelola-akun');

    // Tambah akun baru
    Route::get('/kelola-akun/tambah', [UserController::class, 'create'])->name('kelola-akun.create');
    Route::post('/kelola-akun/tambah', [UserController::class, 'store'])->name('kelola-akun.store');

    // Edit akun
    Route::get('/kelola-akun/{id}/edit', [UserController::class, 'edit'])->name('kelola-akun.edit');
    Route::put('/kelola-akun/{id}', [UserController::class, 'update'])->name('kelola-akun.update');

    // Toggle status aktif/inaktif
    Route::put('/kelola-akun/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('kelola-akun.toggle-status');
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
Route::get('/daftar-barang', [BarangController::class, 'index'])->name('daftar-barang');
Route::get('/tatanan-rack', [RackController::class, 'index'])->name('tatanan-rack');





        Route::middleware(['auth', 'can:superadmin-only'])->group(function () {
        Route::get('/log-aktivitas', [LogController::class, 'index'])->name('log-aktivitas');
    });
// -------------------------
// Auth Routes
// -------------------------
require __DIR__.'/auth.php';
