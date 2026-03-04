<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['deposit', 'referral', 'profit_share', 'bonus', 'suspicious', 'withdrawal'])->index();
            $table->string('reference_id')->nullable()->comment('Links to deposit/withdrawal/user ID');
            $table->decimal('amount', 15, 2);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index('user_id');
            $table->index('created_at');
            $table->comment('Immutable ledger table - never update rows');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('earnings');
    }
};
