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
        Schema::table('skills', function (Blueprint $table) {
            // Add composite index for skill_area_id and name to improve query performance
            $table->index(['skill_area_id', 'name'], 'skills_area_name_index');

            // Add unique index on id to ensure no duplicate IDs
            $table->unique('id', 'skills_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            // First drop the foreign key constraint that references this index
            try {
                $table->dropForeign(['skill_area_id']);
            } catch (\Exception $e) {
                // Foreign key might not exist, ignore errors
            }

            $table->dropIndex('skills_area_name_index');
            $table->dropUnique('skills_id_unique');
        });
    }
};
