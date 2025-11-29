<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Instead of trying to drop the existing constraint,
        // we'll create a new index for active relationships
        Schema::table('coach_student', function (Blueprint $table) {
            // Create an index for active coach-student relationships per team
            $table->index(['coach_id', 'student_id', 'team_id', 'active'], 'coach_student_active_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coach_student', function (Blueprint $table) {
            // Drop the index we created
            $table->dropIndex('coach_student_active_index');
        });
    }
};
