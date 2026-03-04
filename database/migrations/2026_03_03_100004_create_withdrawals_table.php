<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('requested_amount', 15, 2);
            $table->decimal('deduction_amount', 15, 2)->comment('5% deduction');
            $table->decimal('net_amount', 15, 2);
            $table->enum('status', ['pending_otp', 'pending_approval', 'approved', 'rejected', 'cancelled'])->default('pending_otp');
            $table->string('wallet_address')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
