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
        Schema::table('users', function (Blueprint $table) {
            // Add parent information fields
            $table->string('parent1_name')->nullable();
            $table->string('parent1_contact')->nullable();
            $table->string('parent2_name')->nullable();
            $table->string('parent2_contact')->nullable();

            // Add student contact information
            $table->string('phone')->nullable();
            $table->string('school')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'parent1_name',
                'parent1_contact',
                'parent2_name',
                'parent2_contact',
                'phone',
                'school',
            ]);
        });
    }
};
