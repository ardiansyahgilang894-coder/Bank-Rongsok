<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;

class OtpService
{
    public function generate(): int
    {
        return rand(100000, 999999);
    }

    public function send(User $user): void
    {
        $otp = $this->generate();

        $user->update([
            'otp' => $otp,
            'otp_expired_at' => now()->addMinutes(5),
        ]);

        Mail::to($user->email)->send(new SendOtpMail($otp));
    }
}