<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('tx_hash')->nullable()->unique();
            $table->enum('status', ['pending', 'confirmed', 'rejected'])->default('pending');
            $table->string('network')->default('BNB');
            $table->text('metadata')->nullable()->comment('JSON data');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
