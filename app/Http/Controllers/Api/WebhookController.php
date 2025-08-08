<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Verifikasi payload webhook dari Supabase
        // Contoh sederhana: pastikan ada data user
        $userData = $request->input('record');
        if ($userData && $userData['email']) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['email'], // Atau nama lain jika ada
                    'password' => Hash::make('a-random-generated-password'), // Password palsu karena tidak digunakan
                    // Tambahkan kolom lain jika ada, seperti `id_supabase`
                ]
            );
            Log::info("Pengguna baru disinkronkan: " . $user->email);
        }
        return response()->json(['status' => 'ok']);
    }
}


?>