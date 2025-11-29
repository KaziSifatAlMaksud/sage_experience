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
        Schema::table('coach_student', function (Blueprint $table) {
            // First drop the incorrect unique constraint if it exists
            // The error shows it's named 'coach_student_coach_id_student_id_unique'
            try {
                if (Schema::hasIndex('coach_student', 'coach_student_coach_id_student_id_unique')) {
                    $table->dropUnique('coach_student_coach_id_student_id_unique');
                }
            } catch (\Exception $e) {
                // Ignore errors if index doesn't exist
            }

            // Delete duplicate entries before adding new unique constraint
            DB::statement('DELETE t1 FROM coach_student t1
                INNER JOIN coach_student t2
                WHERE
                    t1.id < t2.id AND
                    t1.coach_id = t2.coach_id AND
                    t1.student_id = t2.student_id');

            // Also delete any duplicates that might exist with the old constraint
            DB::statement('DELETE t1 FROM coach_student t1
                INNER JOIN coach_student t2
                WHERE
                    t1.id < t2.id AND
                    t1.coach_id = t2.coach_id AND
                    t1.student_id = t2.student_id AND
                    t1.team_id = t2.team_id');

            // Create the correct unique constraint including team_id
            try {
                $table->unique(['coach_id', 'student_id', 'team_id'], 'coach_student_coach_student_team_unique');
            } catch (\Exception $e) {
                // Index might already exist, ignore errors
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coach_student', function (Blueprint $table) {
            if (Schema::hasIndex('coach_student', 'coach_student_coach_student_team_unique')) {
                $table->dropUnique('coach_student_coach_student_team_unique');
            }

            if (!Schema::hasIndex('coach_student', 'coach_student_coach_id_student_id_unique')) {
                $table->unique(['coach_id', 'student_id'], 'coach_student_coach_id_student_id_unique');
            }
        });
    }
};
