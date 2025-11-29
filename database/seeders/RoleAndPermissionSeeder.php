<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $studentRole = Role::create(['name' => 'student', 'guard_name' => 'web']);
        $subjectMentorRole = Role::create(['name' => 'subject_mentor', 'guard_name' => 'web']);
        $personalCoachRole = Role::create(['name' => 'personal_coach', 'guard_name' => 'web']);
        $projectAdvisorRole = Role::create(['name' => 'project_advisor', 'guard_name' => 'web']);

        // Create permissions using the list of all resources and actions
        $resources = [
            'teams', 'skills', 'skill_areas', 'practices', 'users', 'feedback', 'roles', 'permissions',
        ];

        $actions = [
            'view any', 'view', 'create', 'update', 'delete', 'restore', 'force delete',
        ];

        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                Permission::create(['name' => "{$action} {$resource}", 'guard_name' => 'web']);
            }
        }

        // Special permissions
        Permission::create(['name' => 'access admin panel', 'guard_name' => 'web']);
        Permission::create(['name' => 'access student panel', 'guard_name' => 'web']);
        Permission::create(['name' => 'invite team members', 'guard_name' => 'web']);
        Permission::create(['name' => 'manage coaches', 'guard_name' => 'web']);
        Permission::create(['name' => 'manage students', 'guard_name' => 'web']);
        Permission::create(['name' => 'assign coach to student', 'guard_name' => 'web']);
        Permission::create(['name' => 'transfer student', 'guard_name' => 'web']);
        Permission::create(['name' => 'access skill practice', 'guard_name' => 'web']);

        // Assign admin permissions (all)
        $adminRole->givePermissionTo(Permission::all());

        // Student permissions
        $studentRole->givePermissionTo([
            'view any teams', 'view teams',
            'view any skills', 'view skills',
            'view any skill_areas', 'view skill_areas',
            'view any practices', 'view practices',
            'access student panel',
            'access skill practice',
        ]);

        // Project Advisor permissions (similar to admin but not all)
        $projectAdvisorRole->givePermissionTo([
            'view any teams', 'view teams', 'create teams', 'update teams', 'delete teams',
            'view any skills', 'view skills', 'create skills', 'update skills',
            'view any skill_areas', 'view skill_areas', 'create skill_areas', 'update skill_areas',
            'view any practices', 'view practices', 'create practices', 'update practices',
            'view any users', 'view users', 'create users', 'update users',
            'view any feedback', 'view feedback', 'create feedback', 'update feedback',
            'access admin panel',
            'invite team members',
            'manage coaches',
            'manage students',
            'assign coach to student',
            'transfer student',
        ]);

        // Subject mentor permissions
        $subjectMentorRole->givePermissionTo([
            'view any teams', 'view teams',
            'view any skills', 'view skills',
            'view any skill_areas', 'view skill_areas',
            'view any practices', 'view practices',
            'view any users', 'view users',
            'view any feedback', 'view feedback', 'create feedback', 'update feedback',
            'access admin panel',
        ]);

        // Personal coach permissions
        $personalCoachRole->givePermissionTo([
            'view any teams', 'view teams',
            'view any skills', 'view skills',
            'view any skill_areas', 'view skill_areas',
            'view any practices', 'view practices',
            'view any users', 'view users',
            'view any feedback', 'view feedback', 'create feedback', 'update feedback',
            'access admin panel',
        ]);
    }
}
