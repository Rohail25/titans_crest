<?php

namespace App\Services;

use App\Models\User;
use App\Models\OtpRequest;
use App\Models\EmailLog;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OTPService
{
    const OTP_LENGTH = 6;
    const OTP_EXPIRY_MINUTES = 5;
    const MAX_ATTEMPTS = 3;

    /**
     * Generate and store OTP
     */
    public function generateOTP(User $user, string $purpose = 'withdrawal'): string
    {
        return DB::transaction(function () use ($user, $purpose) {
            // Clear previous pending OTPs for this purpose
            $user->otpRequests()
                ->where('purpose', $purpose)
                ->where('status', 'pending')
                ->update(['status' => 'expired']);

            $otp = str_pad(random_int(0, 999999), self::OTP_LENGTH, '0', STR_PAD_LEFT);
            $expiryMinutes = (int) Setting::get('otp_expiry_minutes', self::OTP_EXPIRY_MINUTES);

            OtpRequest::create([
                'user_id' => $user->id,
                'otp' => bcrypt($otp),
                'purpose' => $purpose,
                'status' => 'pending',
                'expires_at' => now()->addMinutes($expiryMinutes),
                'attempts' => 0,
            ]);

            $this->sendOtpEmail($user, $otp, $purpose, $expiryMinutes);

            return $otp; // Return for testing only
        });
    }

    protected function sendOtpEmail(User $user, string $otp, string $purpose, int $expiryMinutes): void
    {
        $subject = 'Your OTP Code - Titans Crest';
        $body = "Your OTP for {$purpose} is: {$otp}. This code expires in {$expiryMinutes} minutes.";

        $emailLog = EmailLog::create([
            'user_id' => $user->id,
            'recipient' => $user->email,
            'subject' => $subject,
            'body' => $body,
            'type' => 'otp',
            'status' => 'pending',
        ]);

        try {
            Mail::raw($body, function ($message) use ($user, $subject) {
                $message->to($user->email)
                    ->subject($subject);
            });

            $emailLog->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } catch (\Throwable $exception) {
            $emailLog->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
            ]);

            Log::error('OTP email send failed', [
                'user_id' => $user->id,
                'recipient' => $user->email,
                'purpose' => $purpose,
                'error' => $exception->getMessage(),
            ]);

            throw new \Exception('Failed to send OTP email. Please try again.');
        }
    }

    /**
     * Verify OTP
     */
    public function verifyOTP(int $userId, string $otp, string $purpose = 'withdrawal'): bool
    {
        $user = User::find($userId);

        $otpRequest = $user->otpRequests()
            ->where('purpose', $purpose)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (!$otpRequest) {
            return false;
        }

        if ($otpRequest->isExpired()) {
            $otpRequest->update(['status' => 'expired']);
            return false;
        }

        if ($otpRequest->attempts >= self::MAX_ATTEMPTS) {
            $otpRequest->update(['status' => 'cancelled']);
            return false;
        }

        // Increment attempts
        $otpRequest->increment('attempts');

        // Verify OTP
        if (!password_verify($otp, $otpRequest->otp)) {
            return false;
        }

        // Mark as verified
        $otpRequest->update([
            'status' => 'verified',
            'verified_at' => now(),
        ]);

        return true;
    }

    /**
     * Get pending OTP for user
     */
    public function getPendingOTP(User $user, string $purpose = 'withdrawal'): ?OtpRequest
    {
        return $user->otpRequests()
            ->where('purpose', $purpose)
            ->where('status', 'pending')
            ->latest()
            ->first();
    }

    /**
     * Check if OTP is valid (pending and not expired)
     */
    public function isOTPValid(User $user, string $purpose = 'withdrawal'): bool
    {
        $otp = $this->getPendingOTP($user, $purpose);
        return $otp && !$otp->isExpired() && $otp->attempts < self::MAX_ATTEMPTS;
    }

    /**
     * Get time remaining for OTP expiry
     */
    public function getOTPTimeRemaining(OtpRequest $otp): ?int
    {
        if (!$otp->isPending()) {
            return null;
        }

        $remaining = $otp->expires_at->diffInSeconds(now());
        return max(0, $remaining);
    }

    /**
     * Resend OTP
     */
    public function resendOTP(User $user, string $purpose = 'withdrawal'): string
    {
        return $this->generateOTP($user, $purpose);
    }
}
