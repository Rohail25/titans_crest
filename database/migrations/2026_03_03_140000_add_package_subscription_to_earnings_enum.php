<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            $table->enum('type', ['deposit', 'referral', 'profit_share', 'bonus', 'suspicious', 'withdrawal', 'admin_fund_add', 'admin_fund_deduct', 'package_subscription', 'refund'])
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            $table->enum('type', ['deposit', 'referral', 'profit_share', 'bonus', 'suspicious', 'withdrawal', 'admin_fund_add', 'admin_fund_deduct'])
                ->change();
        });
    }
};
