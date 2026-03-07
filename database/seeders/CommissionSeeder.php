<?php

namespace Database\Seeders;

use App\Models\ReferralCommission;
use App\Models\ProfitSharingLevel;
use Illuminate\Database\Seeder;

class CommissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed referral commission levels
        $referralLevels = [
            ['level' => 1, 'percentage' => 7.00, 'is_active' => true],
            ['level' => 2, 'percentage' => 4.00, 'is_active' => true],
            ['level' => 3, 'percentage' => 2.00, 'is_active' => true],
            ['level' => 4, 'percentage' => 1.00, 'is_active' => true],
            ['level' => 5, 'percentage' => 1.00, 'is_active' => true],
        ];

        foreach ($referralLevels as $level) {
            ReferralCommission::updateOrCreate(
                ['level' => $level['level']],
                $level
            );
        }

        // Seed profit sharing levels
        $profitLevels = [
            ['level' => 1, 'percentage' => 20.00],
            ['level' => 2, 'percentage' => 10.00],
            ['level' => 3, 'percentage' => 5.00],
            ['level' => 4, 'percentage' => 5.00],
            ['level' => 5, 'percentage' => 3.00],
            ['level' => 6, 'percentage' => 3.00],
            ['level' => 7, 'percentage' => 2.00],
            ['level' => 8, 'percentage' => 2.00],
            ['level' => 9, 'percentage' => 2.00],
            ['level' => 10, 'percentage' => 2.00],
        ];

        foreach ($profitLevels as $level) {
            ProfitSharingLevel::updateOrCreate(
                ['level' => $level['level']],
                $level
            );
        }
    }
}
