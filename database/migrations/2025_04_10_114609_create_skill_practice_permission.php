<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the 'access skill practice' permission
        $permission = Permission::create([
            'name' => 'access skill practice',
            'guard_name' => 'web',
        ]);

        // Assign the permission to the student role
        $studentRole = Role::where('name', 'student')->first();
        if ($studentRole) {
            $studentRole->givePermissionTo($permission);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Find and delete the permission
        $permission = Permission::where('name', 'access skill practice')->first();
        if ($permission) {
            $permission->delete();
        }
    }
};
