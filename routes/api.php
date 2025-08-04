<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Rute-rute di sini dimuat oleh RouteServiceProvider dan secara otomatis
| masuk ke dalam grup middleware "api".
|
*/

/**
 * Rute Publik untuk Autentikasi
 * Tidak memerlukan token untuk diakses.
 */
Route::post('/register', [AuthController::class, 'Register']);
Route::post('/login', [AuthController::class, 'Login']);


/**
 * Rute Terproteksi
 * Semua rute di dalam grup ini WAJIB menggunakan token PASETO yang valid.
 */
Route::middleware('auth:api')->group(function () {


    Route::get('/user', function (Request $request) {
        return $request->user();
    });


    Route::post('/logout', [AuthController::class, 'Logout']);


});
