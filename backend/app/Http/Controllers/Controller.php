<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ScrapSale;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        $user = User::where(
            'email',
            $request->email
        )->first();

        if (!$user) {

            return response()->json([
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        if ($user->otp !== $request->otp) {

            return response()->json([
                'message' => 'OTP salah'
            ], 400);
        }

        if (now()->gt($user->otp_expired_at)) {

            return response()->json([
                'message' => 'OTP expired'
            ], 400);
        }

        $user->update([
            'is_verified' => true,
            'otp' => null,
            'otp_expired_at' => null
        ]);

        return response()->json([
            'message' => 'Email berhasil diverifikasi'
        ]);
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where(
            'email',
            $request->email
        )->first();

        if (!$user) {

            return response()->json([
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $otp = rand(100000, 999999);

        $user->update([
            'otp' => $otp,
            'otp_expired_at' =>
            now()->addMinutes(5)
        ]);

        Mail::to($user->email)
            ->send(new SendOtpMail($otp));

        return response()->json([
            'message' =>
            'OTP berhasil dikirim ulang'
        ]);
    }
}
