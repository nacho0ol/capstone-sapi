<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\PengeluaranHarianController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PiutangController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Pengeluaran Harian
//Route::apiResource('pengeluaran', PengeluaranHarianController::class);

// Pesanan
Route::apiResource('pesanan', PesananController::class)->except(['update', 'destroy']);
Route::put('pesanan/{id}/batal', [PesananController::class, 'cancel']);

// Piutang
Route::get('piutang', [PiutangController::class, 'index']);
Route::get('piutang/{id}', [PiutangController::class, 'show']);
Route::put('piutang/{id}/bayar', [PiutangController::class, 'bayar']);

// Laporan
Route::get('laporan/laba-rugi', [LaporanController::class, 'labaRugi']);
