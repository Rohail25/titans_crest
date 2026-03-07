<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update any existing null wallet addresses with a placeholder
        DB::table('withdrawals')
            ->whereNull('wallet_address')
            ->update(['wallet_address' => '0x0000000000000000000000000000000000000000']);
        
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->string('wallet_address')->nullable(false)->change()->comment('User BNB wallet address (required)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->string('wallet_address')->nullable()->change();
        });
    }
};
