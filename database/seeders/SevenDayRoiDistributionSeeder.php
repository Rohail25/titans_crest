<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\UserPackage;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SevenDayRoiDistributionSeeder extends Seeder
{
    /**
     * Reset all ROI to zero, then distribute 7 days ROI for active packages.
     */
    public function run(): void
    {
        $now = now();
        $roiBelow500Percent = (float) Setting::get('roi_below_500_percent', 0.65);
        $roi500PlusPercent = (float) Setting::get('roi_500_plus_percent', 0.75);
        $cycleMinutes = (int) Setting::get('profit_distribution_cycle_minutes', 15);

        $summary = DB::transaction(function () use ($now, $roiBelow500Percent, $roi500PlusPercent, $cycleMinutes) {
            $existingRoiByUser = DB::table('earnings')
                ->selectRaw('user_id, SUM(amount) as total_roi')
                ->where('type', 'profit_share')
                ->groupBy('user_id')
                ->pluck('total_roi', 'user_id');

            $deletedRoiRows = DB::table('earnings')
                ->where('type', 'profit_share')
                ->delete();

            Wallet::query()->chunkById(500, function ($wallets) use ($existingRoiByUser) {
                foreach ($wallets as $wallet) {
                    $oldRoi = (float) ($existingRoiByUser[$wallet->user_id] ?? 0);
                    $newBalance = (float) $wallet->balance - $oldRoi;

                    DB::table('wallets')->where('id', $wallet->id)->update([
                        'balance' => max(0, $newBalance),
                        'total_earned' => 0,
                        'updated_at' => now(),
                    ]);
                }
            });

            UserPackage::query()->update(['total_earned' => 0]);

            $activePackages = UserPackage::query()
                ->where('is_active', true)
                ->where('package_status', 'active')
                ->with('package:id,price')
                ->get();

            $rowsToInsert = [];
            $walletRoiByUser = [];
            $roiByPackageId = [];
            $distributedRows = 0;

            foreach ($activePackages as $userPackage) {
                $principal = (float) ($userPackage->total_deposit ?: $userPackage->package?->price ?: 0);

                if ($principal <= 0) {
                    continue;
                }

                $dailyPercent = $principal < 500 ? $roiBelow500Percent : $roi500PlusPercent;
                $dailyRoi = round($principal * ($dailyPercent / 100), 2);

                if ($dailyRoi <= 0) {
                    continue;
                }

                $packageTotal = 0;

                for ($day = 7; $day >= 1; $day--) {
                    $timestamp = $now->copy()->subDays($day)->setTime(12, 0, 0);

                    $rowsToInsert[] = [
                        'user_id' => $userPackage->user_id,
                        'type' => 'profit_share',
                        'reference_id' => (string) $userPackage->id,
                        'amount' => $dailyRoi,
                        'metadata' => json_encode([
                            'source' => 'seven_day_roi_seed',
                            'user_package_id' => $userPackage->id,
                            'principal' => $principal,
                            'daily_percent' => $dailyPercent,
                            'seed_day_index' => $day,
                        ], JSON_THROW_ON_ERROR),
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];

                    $packageTotal += $dailyRoi;
                    $distributedRows++;
                }

                $walletRoiByUser[$userPackage->user_id] = ($walletRoiByUser[$userPackage->user_id] ?? 0) + $packageTotal;
                $roiByPackageId[$userPackage->id] = $packageTotal;
            }

            if (!empty($rowsToInsert)) {
                foreach (array_chunk($rowsToInsert, 1000) as $chunk) {
                    DB::table('earnings')->insert($chunk);
                }
            }

            foreach ($walletRoiByUser as $userId => $roiAmount) {
                $wallet = Wallet::query()->firstOrCreate(
                    ['user_id' => $userId],
                    [
                        'balance' => 0,
                        'pending_balance' => 0,
                        'suspicious_balance' => 0,
                        'total_deposit' => 0,
                        'total_earned' => 0,
                    ]
                );

                $wallet->increment('balance', $roiAmount);
                $wallet->increment('total_earned', $roiAmount);
            }

            foreach ($roiByPackageId as $userPackageId => $roiAmount) {
                UserPackage::query()
                    ->whereKey($userPackageId)
                    ->update([
                        'total_earned' => $roiAmount,
                        'last_profit_time' => $now,
                        'next_profit_time' => $now->copy()->addMinutes($cycleMinutes),
                    ]);
            }

            return [
                'deleted_roi_rows' => $deletedRoiRows,
                'active_packages' => $activePackages->count(),
                'distributed_rows' => $distributedRows,
                'affected_users' => count($walletRoiByUser),
                'total_distributed' => round(array_sum($walletRoiByUser), 2),
            ];
        });

        if ($this->command) {
            $this->command->info('SevenDayRoiDistributionSeeder completed.');
            $this->command->line('Deleted old ROI rows: ' . $summary['deleted_roi_rows']);
            $this->command->line('Active packages scanned: ' . $summary['active_packages']);
            $this->command->line('New ROI rows inserted: ' . $summary['distributed_rows']);
            $this->command->line('Users credited: ' . $summary['affected_users']);
            $this->command->line('Total ROI distributed: $' . number_format((float) $summary['total_distributed'], 2));
        }
    }
}
