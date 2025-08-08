<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class VerifySupabaseToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        $jwtSecret = env('SUPABASE_JWT_SECRET');
        $supabaseUrl = env('SUPABASE_URL');

        if (!$token || !$jwtSecret) {
            return response()->json(['message' => 'Unauthorized. Token atau secret key tidak ditemukan.'], 401);
        }

        try {

            $decoded = JWT::decode($token, new Key($jwtSecret, 'HS256'));


            if (isset($decoded->exp) && $decoded->exp < time()) {
                throw new ExpiredException('Token expired');
            }
        } catch (ExpiredException $e) {

            $refreshToken = $request->header('X-Refresh-Token') ?? $request->input('refresh_token');

            if (!$refreshToken) {
                return response()->json(['message' => 'Token expired. Refresh token tidak ditemukan.'], 401);
            }


            $response = Http::post("$supabaseUrl/auth/v1/token?grant_type=refresh_token", [
                'refresh_token' => $refreshToken
            ]);

            if ($response->failed()) {
                return response()->json(['message' => 'Gagal refresh token.'], 401);
            }

            $newToken = $response->json()['access_token'] ?? null;
            if (!$newToken) {
                return response()->json(['message' => 'Token baru tidak ditemukan.'], 401);
            }


            return response()->json([
                'message' => 'Token diperbarui',
                'access_token' => $newToken
            ], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Token tidak valid.'], 401);
        }


        $user = User::where('email', $decoded->email)->first();
        if (!$user) {
            return response()->json(['message' => 'Pengguna tidak ditemukan.'], 401);
        }

        Auth::login($user);
        return $next($request);
    }
}