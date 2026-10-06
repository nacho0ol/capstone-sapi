<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\PiutangController;
use App\Http\Controllers\PengeluaranController;

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
use App\Http\Controllers\MasterDataController;

// Rute Kelola Data (Master Data: Produk & Pelanggan)
Route::get('/master', [MasterDataController::class, 'index'])->name('master.index');
Route::post('/master/produk', [MasterDataController::class, 'storeProduk'])->name('master.produk.store');
Route::delete('/master/produk/{id}', [MasterDataController::class, 'deleteProduk'])->name('master.produk.delete');
Route::post('/master/pelanggan', [MasterDataController::class, 'storePelanggan'])->name('master.pelanggan.store');
Route::delete('/master/pelanggan/{id}', [MasterDataController::class, 'deletePelanggan'])->name('master.pelanggan.delete');

use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\ProfilController;

// Rute Keuangan
Route::get('/keuangan', [KeuanganController::class, 'index'])->name('keuangan.index');

// Rute Profil
Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
Route::put('/profil/update', [ProfilController::class, 'update'])->name('profil.update');

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