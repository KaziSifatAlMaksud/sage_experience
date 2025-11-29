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
        Schema::create('user_skill_practices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('skill_area_id')->constrained();
            $table->foreignId('skill_id')->constrained();
            $table->foreignId('practice_id')->constrained();
            $table->integer('selection_number')->comment('Tracks which selection this is (1, 2, or 3)');
            $table->timestamps();

            // Ensure unique combination of user, practice and selection number
            $table->unique(['user_id', 'practice_id', 'selection_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_skill_practices');
    }
};
