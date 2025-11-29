<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Str;

class ProjectAdvisorRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create project_advisor role if it doesn't exist
        $role = Role::firstOrCreate(['name' => 'project_advisor']);

        // Get admin permissions and sync them to project_advisor
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $permissions = $adminRole->permissions->pluck('id')->toArray();
            $role->syncPermissions($permissions);
        }

        // Create a default project advisor user if none exists
        if (User::role('project_advisor')->count() === 0) {
            $user = User::create([
                'name' => 'Project Advisor',
                'email' => 'advisor@sageexperience.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'remember_token' => Str::random(10),
            ]);

            $user->assignRole('project_advisor');
        }
    }
}
