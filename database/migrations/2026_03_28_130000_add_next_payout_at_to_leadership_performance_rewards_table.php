<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('leadership_performance_rewards', 'next_payout_at')) {
            Schema::table('leadership_performance_rewards', function (Blueprint $table) {
                $table->timestamp('next_payout_at')->nullable()->after('next_payout_date');
                $table->index(['is_active', 'next_payout_at'], 'lpr_active_next_at_idx');
            });
        }

        DB::table('leadership_performance_rewards')
            ->whereNull('next_payout_at')
            ;
    }

    public function down(): void
    {
        if (Schema::hasColumn('leadership_performance_rewards', 'next_payout_at')) {
            Schema::table('leadership_performance_rewards', function (Blueprint $table) {
                try {
                    $table->dropIndex('lpr_active_next_at_idx');
                } catch (\Throwable $exception) {
                    // Index might not exist in some rollback states.
                }

                $table->dropColumn('next_payout_at');
            });
        }
    }
};
