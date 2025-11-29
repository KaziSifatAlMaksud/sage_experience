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
        try {
            // Try to drop existing unique index
            Schema::table('team_user', function (Blueprint $table) {
                $table->dropUnique('team_user_team_id_user_id_unique');
            });
        } catch (\Exception $e) {
            // Index might not exist or have a different name, continue
        }

        // Create new unique constraint
        try {
            Schema::table('team_user', function (Blueprint $table) {
                $table->unique(['team_id', 'user_id', 'role'], 'team_user_team_id_user_id_role_unique');
            });
        } catch (\Exception $e) {
            // Constraint might already exist, continue
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            // Drop the new constraint
            Schema::table('team_user', function (Blueprint $table) {
                $table->dropUnique('team_user_team_id_user_id_role_unique');
            });
        } catch (\Exception $e) {}

        try {
            // Restore the original constraint only if it doesn't exist
            Schema::table('team_user', function (Blueprint $table) {
                $table->unique(['team_id', 'user_id'], 'team_user_team_id_user_id_unique');
            });
        } catch (\Exception $e) {}
    }
};
