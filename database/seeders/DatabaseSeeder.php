<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Setting;
use App\Models\Package;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CommissionSeeder::class);

        // Create default settings
        Setting::set('bnb_wallet_address', '0x742d35Cc6634C0532925a3b844Bc9e7595f7D5Cf', 'BNB Wallet Address');
        Setting::set('whatsapp_number', '15551234567', 'WhatsApp Support Number (International Format)');
        Setting::set('referral_commission_percent', '10', 'Referral Commission Percentage');
        Setting::set('daily_profit_rate_standard', '0.0167', 'Standard Daily Profit Rate (1.67%)');
        Setting::set('withdrawal_fee_percent', '5', 'Withdrawal Fee Percentage');
        Setting::set('otp_expiry_minutes', '5', 'OTP Expiry Time in Minutes');
        Setting::set('min_withdrawal_amount', '10', 'Minimum Withdrawal Amount');

        // Create default packages
        Package::firstOrCreate(
            ['name' => 'Basic Package'],
            [
                'price' => 50,
                'daily_profit_rate' => 0.0167,
                'duration_days' => null,
                'is_active' => true,
            ]
        );

        Package::firstOrCreate(
            ['name' => 'Starter Package'],
            [
                'price' => 100,
                'daily_profit_rate' => 0.0167,  // 1.67%
                'duration_days' => null,  // Lifetime
                'is_active' => true,
            ]
        );

        Package::firstOrCreate(
            ['name' => 'Professional Package'],
            [
                'price' => 500,
                'daily_profit_rate' => 0.0167,
                'duration_days' => null,
                'is_active' => true,
            ]
        );

        Package::firstOrCreate(
            ['name' => 'Premium Package'],
            [
                'price' => 1000,
                'daily_profit_rate' => 0.0167,
                'duration_days' => null,
                'is_active' => true,
            ]
        );

        Package::firstOrCreate(
            ['name' => 'Elite Package'],
            [
                'price' => 5000,
                'daily_profit_rate' => 0.0167,
                'duration_days' => null,
                'is_active' => true,
            ]
        );

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('11221122'),
                'referral_code' => strtoupper('ADMIN' . \Illuminate\Support\Str::random(4)),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Create wallet for admin
        $admin->wallet()->firstOrCreate(
            ['user_id' => $admin->id],
            [
                'balance' => 0,
                'pending_balance' => 0,
                'suspicious_balance' => 0,
                'total_deposit' => 0,
                'total_earned' => 0,
            ]
        );

        // Create referral tree for admin
        \App\Models\ReferralTree::firstOrCreate(
            ['user_id' => $admin->id],
            [
                'referral_code' => $admin->referral_code,
                'referrer_id' => null,
                'commission_earned' => 0,
            ]
        );

        // Test user
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password123'),
                'referral_code' => strtoupper('TEST' . \Illuminate\Support\Str::random(4)),
                'role' => 'user',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Create wallet for user
        $user->wallet()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'balance' => 100,
                'pending_balance' => 0,
                'suspicious_balance' => 0,
                'total_deposit' => 100,
                'total_earned' => 0,
            ]
        );

        // Create referral tree
        \App\Models\ReferralTree::firstOrCreate(
            ['user_id' => $user->id],
            [
                'referral_code' => $user->referral_code,
                'referrer_id' => null,
                'commission_earned' => 0,
            ]
        );
    }
}
