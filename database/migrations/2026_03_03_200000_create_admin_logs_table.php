<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->string('action');
            $table->string('target_type')->nullable()->comment('User, Withdrawal, etc');
            $table->foreignId('target_id')->nullable();
            $table->text('old_values')->nullable()->comment('JSON');
            $table->text('new_values')->nullable()->comment('JSON');
            $table->text('reason')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
            $table->index(['admin_id', 'created_at']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_logs');
    }
};
