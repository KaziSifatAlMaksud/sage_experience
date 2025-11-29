<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the project_advisor role
        $role = Role::create(['name' => 'project_advisor']);

        // Get the existing admin permissions
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminPermissions = $adminRole->permissions;

            // Assign the same permissions to project_advisor role
            $role->syncPermissions($adminPermissions);
        }

        // Also add the role to the roles table in teams migration
        Schema::table('teams', function (Blueprint $table) {
            // Add subject mentor and project advisor fields if they don't exist
            if (!Schema::hasColumn('teams', 'project_advisor_id')) {
                $table->foreignId('project_advisor_id')->nullable()->constrained('users');
            }

            if (!Schema::hasColumn('teams', 'subject_mentor_id')) {
                $table->foreignId('subject_mentor_id')->nullable()->constrained('users');
            }

            // Add status field
            if (!Schema::hasColumn('teams', 'status')) {
                $table->string('status')->default('active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the role
        $role = Role::where('name', 'project_advisor')->first();
        if ($role) {
            $role->delete();
        }

        // Remove the columns (only if needed)
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropConstrainedForeignId('project_advisor_id');
            $table->dropConstrainedForeignId('subject_mentor_id');
        });
    }
};
