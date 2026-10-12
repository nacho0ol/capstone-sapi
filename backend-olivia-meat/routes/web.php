<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\PiutangController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\ProfilController;

// Rute Autentikasi Pengguna (Sesuai Proposal Bab 2: Activity Diagram Login)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Rute yang Memerlukan Hak Akses Login
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Halaman Utama / Dashboard (Ringkasan Keuangan & Laba Rugi)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Rute Transaksi Pesanan (Create & Read)
    Route::get('/transaksi', [PesananController::class, 'index'])->name('transaksi.index');
    Route::post('/transaksi/store', [PesananController::class, 'store'])->name('transaksi.store');
    Route::get('/transaksi/{id}/edit', [PesananController::class, 'edit'])->name('transaksi.edit');
    Route::put('/transaksi/{id}', [PesananController::class, 'update'])->name('transaksi.update');
    Route::post('/transaksi/cancel/{id}', [PesananController::class, 'cancel'])->name('transaksi.cancel');

    // Rute Piutang & Update Pembayaran Cicilan
    Route::get('/piutang', [PiutangController::class, 'index'])->name('piutang.index');
    Route::post('/piutang/bayar/{id}', [PiutangController::class, 'updatePembayaran'])->name('piutang.bayar');

    // Rute Pengeluaran Harian
    Route::get('/pengeluaran', [PengeluaranController::class, 'index'])->name('pengeluaran.index');
    Route::post('/pengeluaran/store', [PengeluaranController::class, 'store'])->name('pengeluaran.store');
    Route::put('/pengeluaran/{id}', [PengeluaranController::class, 'update'])->name('pengeluaran.update');
    Route::delete('/pengeluaran/{id}', [PengeluaranController::class, 'destroy'])->name('pengeluaran.destroy');

    // Rute Kelola Data (Master Data: Produk & Pelanggan)
    Route::get('/master', [MasterDataController::class, 'index'])->name('master.index');

    // Rute CRUD Produk
    Route::post('/master/produk', [MasterDataController::class, 'storeProduk'])->name('master.produk.store');
    Route::put('/master/produk/{id}', [MasterDataController::class, 'updateProduk'])->name('master.produk.update');
    Route::delete('/master/produk/{id}', [MasterDataController::class, 'deleteProduk'])->name('master.produk.delete');

    // Rute CRUD Pelanggan
    Route::post('/master/pelanggan', [MasterDataController::class, 'storePelanggan'])->name('master.pelanggan.store');
    Route::put('/master/pelanggan/{id}', [MasterDataController::class, 'updatePelanggan'])->name('master.pelanggan.update');
    Route::delete('/master/pelanggan/{id}', [MasterDataController::class, 'deletePelanggan'])->name('master.pelanggan.delete');

    // Rute Keuangan
    Route::get('/keuangan', [KeuanganController::class, 'index'])->name('keuangan.index');
    Route::post('/pengaturan/notifikasi', [KeuanganController::class, 'updateNotifikasi'])->name('pengaturan.notifikasi');
    Route::post('/pengaturan/notifikasi/test', [KeuanganController::class, 'testNotifikasi'])->name('pengaturan.notifikasi.test');
    Route::post('/pengaturan/notifikasi/kirim', [KeuanganController::class, 'kirimPengingatManual'])->name('pengaturan.notifikasi.kirim');

    // Rute Profil
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
    Route::put('/profil/update', [ProfilController::class, 'update'])->name('profil.update');
});