<?php

namespace App\Http\Controllers\Api;

use App\Helpers\PasetoHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Menangani permintaan registrasi pengguna baru.
     * Memvalidasi input, membuat pengguna baru, dan mengembalikan data pengguna.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
public function Register(Request $request)
{
    $validatedData = $request->validate([
        'name' => 'required|string|max:100',
        'email' => 'required|string|email|max:150|unique:users',
        'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
    ]);

    $userData = array_merge(
        $validatedData,
        ['role' => 'user']
    );

    $user = User::create($userData);

    return response()->json([
        'message' => 'Berhasil Melakukan Registrasi.',
        'user' => new UserResource($user)
    ], 201);
}

    /**
     * Menangani permintaan login pengguna.
     * Memvalidasi kredensial dan membuat token PASETO jika berhasil.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function Login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid Credentials.'
            ], 401);
        }

        $payload = [
            'sub' => $user->id,
            'role' => $user->role,
            'email' => $user->email,
        ];

        $token = PasetoHelper::createToken($payload);

        return response()->json([
            'message' => 'Login Berhasil',
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Menangani permintaan logout pengguna.
     * Menginvalidasi token saat ini dengan menambahkannya ke daftar hitam (blacklist).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function Logout(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['message' => 'Token tidak ditemukan.'], 401);
        }

        $remainingTimeInSeconds = PasetoHelper::getRemainingExpiry($token);


        if ($remainingTimeInSeconds > 0) {
            Cache::put('token_blacklist:' . $token, true, $remainingTimeInSeconds);
        }

        return response()->json([
            'message' => 'Logout Sukses. Token telah diinvalidasi.'
        ]);
    }
}
