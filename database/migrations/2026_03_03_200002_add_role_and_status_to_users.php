<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['user', 'admin'])->default('user')->after('email');
            $table->enum('status', ['active', 'inactive', 'banned', 'suspended'])->default('active')->after('role');
            $table->text('ban_reason')->nullable()->after('status');
            $table->timestamp('banned_at')->nullable()->after('ban_reason');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'status', 'ban_reason', 'banned_at']);
        });
    }
};
