<?php

namespace App\Repositories;

use App\Http\Requests\AuthRequest;
use App\Interfaces\AuthInterfaces;
use App\Models\User;
use App\Traits\HttpResponseTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthRepositories implements AuthInterfaces
{
    use HttpResponseTraits;
    protected $usermodel;
    public function __construct(User $usermodel)
    {
        $this->usermodel = $usermodel;
    }
    public function login(AuthRequest $request)
    {
        try {
            // 1. Coba melakukan autentikasi
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Email atau password salah'
                ], 401); // 401 Unauthorized
            }

            // 2. Jika berhasil, ambil data user
            $user = Auth::user(); // Lebih ringkas daripada query ulang

            // Buat token (opsional jika Anda menggunakan Sanctum untuk API)
            $token = $user->createToken('token')->plainTextToken;

            // 3. Kembalikan response JSON sukses dan URL tujuan
            return response()->json([
                'status'   => 'success',
                'message'  => 'Login berhasil',
                'token'    => $token
            ], 200);
        } catch (\Throwable $th) {
            // 4. Tangani error sistem
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan pada server: ' . $th->getMessage()
            ], 500); // 500 Internal Server Error
        }
    }



    public function logout(Request $request)
    {
        try {
            $request->user('web')->tokens()->delete();

            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return $this->success();
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400, $th, class_basename($this), __FUNCTION__);
        }
    }
}
