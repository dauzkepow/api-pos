<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthenticateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // login, verifikasi email dan password lalu berikan bearer token jika data sesuai
    public function login(AuthenticateUserRequest $request)
    {
        // buat variabel untuk ambil data input email dan password yang divalidasi AuthenticateUserRequest
        $credentials = $request->validated();

        // cari user berdasarkan email yang diinputkan
        $user = User::where('email', $credentials['email'])->first();

        // pengecekan email tidak ditemukan atau password tidak sesuai
        if (!$user || !Hash::check($credentials['password'], $user->password))
            {
                // return pengecekan tidak valid panggil Helper ApiResponse.php
                return ApiResponse::error(
                    'Invalid credentials',
                    Response::HTTP_UNAUTHORIZED
                );
            }

            // jika user sesuai, auth_token = kunci gerbang
            $token = $user->createToken('auth_token')->plainTextToken;
            return ApiResponse::success(
                [
                    'token' => $token,
                    // 'user' => $user, // cara-1 tampilkan semua
                    'user' => new UserResource($user)
                ],
                'Login Successfully'
            );
    }
}
