<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ReferralTree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            if (Auth::user()->role === 'admin') {
                return redirect()->intended('/admin');
            }

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records. Please check your email and password.',
        ])->withInput($request->except('password'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|string|min:6|confirmed',
            'referral_code' => 'nullable|string|exists:referral_tree,referral_code',
            'terms' => 'accepted',
        ]);

        $referrerId = null;
        if (!empty($validated['referral_code'])) {
            $referrer = ReferralTree::where('referral_code', strtoupper($validated['referral_code']))->first();
            $referrerId = $referrer?->user_id;
        }
        $baseCode = strtolower(preg_replace('/\s+/', '', $validated['name']));
        do {
            $referralCode = $baseCode . rand(10, 99);
        } while (User::where('referral_code', $referralCode)->exists());

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            // 'referral_code' => strtoupper(Str::random(8)),
            'referral_code' => $referralCode,
            'referred_by' => $referrerId,
            'role' => 'user',
            'status' => 'active',
        ]);

        // Create wallet for new user
        $user->wallet()->create([
            'balance' => 0,
            'pending_balance' => 0,
            'suspicious_balance' => 0,
            'total_deposit' => 0,
            'total_earned' => 0,
        ]);

        // Create referral tree for new user
        \App\Models\ReferralTree::create([
            'user_id' => $user->id,
            'referral_code' => $user->referral_code,
            'referrer_id' => $referrerId,
            'commission_earned' => 0,
        ]);

        Auth::login($user);
        return redirect('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
        
        return redirect('/');
    }
}
