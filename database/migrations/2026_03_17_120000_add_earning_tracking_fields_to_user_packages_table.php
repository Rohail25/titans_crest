<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_packages', function (Blueprint $table) {
            $table->decimal('total_deposit', 15, 2)->default(0)->after('is_active');
            $table->decimal('total_earned', 15, 2)->default(0)->after('total_deposit');
            $table->decimal('earning_cap', 15, 2)->default(0)->after('total_earned');
            $table->string('package_status', 20)->default('active')->after('earning_cap');
            $table->timestamp('last_profit_time')->nullable()->after('package_status');
            $table->timestamp('next_profit_time')->nullable()->after('last_profit_time');

            $table->index(['is_active', 'package_status'], 'user_packages_active_status_idx');
            $table->index('next_profit_time');
        });
    }

    public function down(): void
    {
        Schema::table('user_packages', function (Blueprint $table) {
            $table->dropIndex('user_packages_active_status_idx');
            $table->dropIndex(['next_profit_time']);
            $table->dropColumn([
                'total_deposit',
                'total_earned',
                'earning_cap',
                'package_status',
                'last_profit_time',
                'next_profit_time',
            ]);
        });
    }
};
