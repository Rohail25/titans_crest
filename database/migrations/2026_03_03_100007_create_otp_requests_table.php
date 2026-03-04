<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('otp');
            $table->string('purpose')->comment('withdrawal, email_verification, etc');
            $table->enum('status', ['pending', 'verified', 'expired', 'cancelled'])->default('pending');
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->integer('attempts')->default(0);
            $table->timestamps();
            $table->index(['user_id', 'purpose', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_requests');
    }
};
