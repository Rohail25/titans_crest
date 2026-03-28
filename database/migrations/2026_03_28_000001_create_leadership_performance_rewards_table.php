<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leadership_performance_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sponsor_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('trigger_reference_id')->nullable();
            $table->decimal('instant_commission_amount', 15, 2);
            $table->decimal('daily_bonus_amount', 15, 2);
            $table->unsignedSmallInteger('total_days')->default(100);
            $table->unsignedSmallInteger('payouts_remaining')->default(100);
            $table->date('next_payout_date');
            $table->timestamp('next_payout_at')->nullable();
            $table->timestamp('last_paid_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'next_payout_date'], 'lpr_active_next_date_idx');
            $table->index(['is_active', 'next_payout_at'], 'lpr_active_next_at_idx');
            $table->index('sponsor_user_id');
            $table->index('referred_user_id');
            $table->index('trigger_reference_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leadership_performance_rewards');
    }
};
