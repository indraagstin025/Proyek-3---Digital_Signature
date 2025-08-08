<?php

namespace App\Http\Controllers\Api;

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
}
