<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration ensures a student can have:
     * 1. Only one active coach per team
     * 2. Multiple coaches across different teams
     */
    public function up(): void
    {
        // First, ensure the active column exists
        if (!Schema::hasColumn('coach_student', 'active')) {
            Schema::table('coach_student', function (Blueprint $table) {
                $table->boolean('active')->default(true)->after('team_id');
            });
        }

        // Now, make sure we have only one active coach per student per team
        $duplicates = DB::table('coach_student')
            ->select('student_id', 'team_id')
            ->where('active', true)
            ->groupBy('student_id', 'team_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            // Get all active coach relationships for this student in this team
            $relationships = DB::table('coach_student')
                ->where('student_id', $duplicate->student_id)
                ->where('team_id', $duplicate->team_id)
                ->where('active', true)
                ->orderBy('created_at', 'desc')
                ->get();

            // Keep only the most recent one active
            if ($relationships->count() > 1) {
                $mostRecent = $relationships->shift(); // Remove and get the first (most recent)

                // Set all others to inactive
                foreach ($relationships as $rel) {
                    DB::table('coach_student')
                        ->where('id', $rel->id)
                        ->update(['active' => false]);
                }
            }
        }

        // Add notes column if it doesn't exist
        if (!Schema::hasColumn('coach_student', 'notes')) {
            Schema::table('coach_student', function (Blueprint $table) {
                $table->text('notes')->nullable()->after('active');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration needed as this is a cleanup migration
    }
};
