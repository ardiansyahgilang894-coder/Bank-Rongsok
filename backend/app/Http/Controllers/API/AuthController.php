<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\OtpService;

class AuthController extends Controller
{

    public function register(Request $request, OtpService $otpService)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $otpService->send($user);

        return response()->json([
            'message' => 'Register berhasil. OTP dikirim ke email.'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        if ($user->is_verified) {
            return response()->json(['message' => 'User sudah diverifikasi']);
        }

        if ($user->otp !== $request->otp) {
            return response()->json(['message' => 'OTP salah'], 400);
        }

        if (now()->greaterThan($user->otp_expired_at)) {
            return response()->json(['message' => 'OTP expired'], 400);
        }

        $user->update([
            'is_verified' => true,
            'otp' => null,
            'otp_expired_at' => null,
        ]);

        return response()->json(['message' => 'Email berhasil diverifikasi']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah'
            ], 401);
        }

        if (!$user->is_verified) {
            return response()->json(['message' => 'Akun belum diverifikasi OTP'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }
}
