<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\User\DocumentController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;


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
 * Khusus untuk proses register jika Anda menanganinya di backend Laravel.
 * Proses login akan sepenuhnya dilakukan di frontend dengan Supabase SDK.
 */
Route::post('/register', [AuthController::class, 'Register']);


/**
 * Rute Terproteksi
 * Semua rute di dalam grup ini WAJIB menggunakan token JWT dari Supabase yang valid.
 */
Route::middleware('auth.supabase')->group(function () {

    Route::get('/user', function (Request $request) {
        return new UserResource($request->user());
    });

    Route::apiResource('documents', DocumentController::class);

});

Route::post('/supabase-webhook', [WebhookController::class, 'handle']);

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});
