<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BarangController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransaksiController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Barang routes - menggunakan resource controller
    Route::resource('barangs', BarangController::class);

    // Alternatif: jika ingin mendefinisikan manual satu per satu
    // Route::get('/barangs', [BarangController::class, 'index'])->name('barangs.index');
    // Route::get('/barangs/create', [BarangController::class, 'create'])->name('barangs.create');
    // Route::post('/barangs', [BarangController::class, 'store'])->name('barangs.store');
    // Route::get('/barangs/{barang}', [BarangController::class, 'show'])->name('barangs.show');
    // Route::get('/barangs/{barang}/edit', [BarangController::class, 'edit'])->name('barangs.edit');
    // Route::put('/barangs/{barang}', [BarangController::class, 'update'])->name('barangs.update');
    // Route::delete('/barangs/{barang}', [BarangController::class, 'destroy'])->name('barangs.destroy');

    // Tambahkan di dalam middleware auth
    Route::post('/transaksis', [TransaksiController::class, 'store'])->name('transaksis.store');
    Route::get('/transaksis/{id}', [TransaksiController::class, 'show'])->name('transaksis.show');

    Route::post('transaksis/add-item', [TransaksiController::class, 'addItem'])->name('transaksis.addItem');
    Route::post('transaksis/remove-item', [TransaksiController::class, 'removeItem'])->name('transaksis.removeItem');
    Route::post('transaksis/process-payment', [TransaksiController::class, 'processPayment'])->name('transaksis.processPayment');
});

require __DIR__ . '/auth.php';
