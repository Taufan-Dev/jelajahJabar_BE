<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WisataController;
use App\Http\Controllers\Api\TiketController;
use App\Http\Controllers\Api\RekomendasiController;
use App\Http\Controllers\Api\ValidasiTiketController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
// Autentikasi
Route::post('/register', [AuthController::class, 'registerUser']);
Route::post('/register-user', [AuthController::class, 'registerUser']);
Route::post('/login', [AuthController::class, 'login']);

// Katalog Wisata (Umum / Guest)
Route::get('/wisata', [WisataController::class, 'index']);
Route::get('/wisata/{id}', [WisataController::class, 'show']);
Route::get('/kategori', [WisataController::class, 'categories']);

// Callback Midtrans (Public Webhook & Simulator)
Route::post('/payment/callback', [TiketController::class, 'callback']);
Route::post('/payment/simulate-callback', [TiketController::class, 'simulateCallback']);

// QR Code Tiket (Public untuk mempermudah image loading di Android)
Route::get('/tiket/{kode_tiket}/qrcode', [TiketController::class, 'qrCode']);

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Sanctum Protected)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Autentikasi & Profil
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Pendaftaran & Update Wisata (Pengelola)
    Route::post('/wisata', [WisataController::class, 'store']);
    Route::post('/wisata/{id}', [WisataController::class, 'update']); // Menggunakan POST agar support Form-Data file upload dari mobile

    // Persetujuan Wisata (Admin Wilayah & Super Admin)
    Route::post('/wisata/{id}/approve-admin', [WisataController::class, 'approveAdmin']);
    Route::post('/wisata/{id}/approve-super', [WisataController::class, 'approveSuper']);

    // Pemesanan & Riwayat Tiket
    Route::post('/tiket', [TiketController::class, 'store']);
    Route::get('/tiket', [TiketController::class, 'index']);

    // Ulasan / Rating Wisata
    Route::post('/rekomendasi', [RekomendasiController::class, 'store']);

    // Validasi Pintu Masuk Tiket (QR Scanner Pengelola)
    Route::post('/validasi-tiket', [ValidasiTiketController::class, 'validateTicket']);
});

