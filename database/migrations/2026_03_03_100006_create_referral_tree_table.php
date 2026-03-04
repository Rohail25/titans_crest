<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_tree', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('referrer_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('referral_code')->unique();
            $table->decimal('commission_earned', 15, 2)->default(0);
            $table->integer('level')->default(1);
            $table->timestamps();
            $table->index('referrer_id');
            $table->index('referral_code');
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_tree');
    }
};
