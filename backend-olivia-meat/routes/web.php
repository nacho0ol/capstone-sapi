<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\PiutangController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PemasokanDagingController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\ProfilController;

// Rute Autentikasi Pengguna
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/forgot-password', [AuthController::class, 'showLinkRequestForm'])->name('password.request')->middleware('guest');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email')->middleware('guest');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset')->middleware('guest');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update')->middleware('guest');

// Rute yang Memerlukan Hak Akses Login
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // 1. Beranda
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Orderan (Transaksi Orderan)
    Route::get('/transaksi', [PesananController::class, 'index'])->name('transaksi.index');
    Route::post('/transaksi/store', [PesananController::class, 'store'])->name('transaksi.store');
    Route::get('/transaksi/{id}/edit', [PesananController::class, 'edit'])->name('transaksi.edit');
    Route::put('/transaksi/{id}', [PesananController::class, 'update'])->name('transaksi.update');
    Route::post('/transaksi/cancel/{id}', [PesananController::class, 'cancel'])->name('transaksi.cancel');
    Route::put('/transaksi/{id}/komplain', [PesananController::class, 'updateKomplain'])->name('transaksi.komplain');

    // 3. Pemasokan Daging
    Route::get('/pemasokan-daging', [PemasokanDagingController::class, 'index'])->name('pemasokan.index');
    Route::post('/pemasokan-daging', [PemasokanDagingController::class, 'store'])->name('pemasokan.store');
    Route::put('/pemasokan-daging/{id}', [PemasokanDagingController::class, 'update'])->name('pemasokan.update');
    Route::delete('/pemasokan-daging/{id}', [PemasokanDagingController::class, 'destroy'])->name('pemasokan.destroy');

    // 4. Produk & Jagal
    Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
    Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
    Route::put('/produk/{id}', [ProdukController::class, 'update'])->name('produk.update');
    Route::delete('/produk/{id}', [ProdukController::class, 'destroy'])->name('produk.destroy');

    Route::post('/jagal', [ProdukController::class, 'storeJagal'])->name('jagal.store');
    Route::put('/jagal/{id}', [ProdukController::class, 'updateJagal'])->name('jagal.update');
    Route::delete('/jagal/{id}', [ProdukController::class, 'destroyJagal'])->name('jagal.destroy');

    // 5. Pelanggan (+ History Pesanan, Pembayaran & Komplain)
    Route::get('/pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
    Route::get('/pelanggan/{id}', [PelangganController::class, 'show'])->name('pelanggan.show');
    Route::post('/pelanggan', [PelangganController::class, 'store'])->name('pelanggan.store');
    Route::put('/pelanggan/{id}', [PelangganController::class, 'update'])->name('pelanggan.update');
    Route::delete('/pelanggan/{id}', [PelangganController::class, 'destroy'])->name('pelanggan.destroy');

    // 6. Keuangan, Tagihan (Piutang) & Pengeluaran
    Route::get('/keuangan', [KeuanganController::class, 'index'])->name('keuangan.index');
    Route::post('/pengaturan/notifikasi', [KeuanganController::class, 'updateNotifikasi'])->name('pengaturan.notifikasi');
    Route::post('/pengaturan/notifikasi/test', [KeuanganController::class, 'testNotifikasi'])->name('pengaturan.notifikasi.test');
    Route::post('/pengaturan/notifikasi/kirim', [KeuanganController::class, 'kirimPengingatManual'])->name('pengaturan.notifikasi.kirim');

    Route::get('/piutang', [PiutangController::class, 'index'])->name('piutang.index');
    Route::post('/piutang/bayar/{id}', [PiutangController::class, 'updatePembayaran'])->name('piutang.bayar');

    Route::get('/pengeluaran', [PengeluaranController::class, 'index'])->name('pengeluaran.index');
    Route::post('/pengeluaran/store', [PengeluaranController::class, 'store'])->name('pengeluaran.store');
    Route::put('/pengeluaran/{id}', [PengeluaranController::class, 'update'])->name('pengeluaran.update');
    Route::delete('/pengeluaran/{id}', [PengeluaranController::class, 'destroy'])->name('pengeluaran.destroy');

    // 7. Profil
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
    Route::put('/profil/update', [ProfilController::class, 'update'])->name('profil.update');
    Route::put('/profil/password', [ProfilController::class, 'updatePassword'])->name('profil.password.update');
});