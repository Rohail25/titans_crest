<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetEmail;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PasswordResetController extends Controller
{
    /**
     * Show change password form (Edit Profile)
     */
    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    /**
     * Send password change email verification
     * User enters current password for verification (from Edit Profile)
     */
    public function sendChangePasswordEmail(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string|min:6',
        ]);

        $user = auth()->user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        // Generate reset token
        $token = Str::random(60);

        // Store token in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // Send email with reset link
        Mail::to($user->email)->send(
            new PasswordResetEmail($user, $token, 'change-password')
        );

        return back()->with('message', 'Password reset link sent to your email. Please check your inbox.');
    }

    /**
     * Show forgot password form (Login Page)
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset email for forgot password flow
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Check if email exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email not found in our system.']);
        }

        // Generate reset token
        $token = Str::random(60);

        // Store token in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // Send email with reset link
        Mail::to($user->email)->send(
            new PasswordResetEmail($user, $token, 'reset-password')
        );

        return back()->with('message', 'Password reset link sent to your email. Please check your inbox.');
    }

    /**
     * Show password reset form (from email link)
     */
    public function showResetForm(Request $request)
    {
        $token = $request->token;
        $email = $request->email;

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Update password (from email link)
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Get the stored token
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
            return back()->withErrors(['token' => 'Invalid or expired reset token.']);
        }

        // Check token age (valid for 1 hour)
        if (now()->diffInMinutes($resetRecord->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['token' => 'Password reset link has expired. Please request a new one.']);
        }

        // Update password
        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Delete reset token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Fire password reset event
        event(new PasswordReset($user));

        // Log user in
        auth()->login($user);

        return redirect()->route('user.dashboard')->with('success', 'Password changed successfully! You are now logged in.');
    }
}
