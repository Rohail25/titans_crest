<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_performance_excellence_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sponsor_user_id')->constrained('users')->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('total_volume', 15, 2)->default(0);
            $table->unsignedInteger('qualified_legs')->default(0);
            $table->decimal('qualifying_tier_volume', 15, 2)->nullable();
            $table->decimal('qualifying_tier_reward', 15, 2)->default(0);
            $table->unsignedInteger('qualifying_tier_min_legs')->nullable();
            $table->string('status', 32)->default('not_qualified');
            $table->timestamp('paid_at')->nullable();
            $table->json('leg_volumes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['sponsor_user_id', 'period_start', 'period_end'], 'mper_unique_sponsor_period');
            $table->index(['period_start', 'period_end'], 'mper_period_idx');
            $table->index(['status', 'paid_at'], 'mper_status_paid_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_performance_excellence_rewards');
    }
};
