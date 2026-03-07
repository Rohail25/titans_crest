<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profit_sharing_levels', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('level')->comment('Profit sharing level 1-10');
            $table->decimal('percentage', 5, 2)->comment('Profit sharing percentage');
            $table->timestamps();
            $table->unique('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profit_sharing_levels');
    }
};
