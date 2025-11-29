<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Team;
use App\Models\CoachStudent;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class DummyStudentsAndTeamsSeeder extends Seeder
{
    public function run()
    {
        // Create 3 personal coaches
        $coaches = collect();
        foreach (range(1, 3) as $i) {
            $coaches->push(User::firstOrCreate(
                [ 'email' => "coach$i@example.com" ],
                [
                    'name' => "Personal Coach $i",
                    'password' => bcrypt('password'),
                    'role' => 'personal_coach',
                ]
            ));
        }
        foreach ($coaches as $coach) {
            $coach->assignRole('personal_coach');
        }

        // Create 5 dummy students
        $students = collect();
        foreach (range(1, 5) as $i) {
            $students->push(User::firstOrCreate(
                [ 'email' => "student$i@example.com" ],
                [
                    'name' => "Student $i",
                    'password' => bcrypt('password'),
                    'role' => 'student',
                    'student_phone' => '0312'.rand(1000000,9999999),
                    'student_school' => 'Test School '.Str::random(4),
                    'parent1_name' => 'Parent1-'.$i,
                    'parent1_contact' => '+92-300-000000'.$i,
                    'parent2_name' => 'Parent2-'.$i,
                    'parent2_contact' => '+92-301-000000'.$i,
                ]
            ));
        }
        foreach ($students as $student) {
            $student->assignRole('student');
        }

        // Create 2 teams
        $teams = collect();
        foreach (range(1, 2) as $i) {
            $teams->push(Team::firstOrCreate(
                ['name' => "Team $i"],
                [
                    'status' => 'active',
                    'created_by' => 1,
                ]
            ));
        }

        // For each student, assign a random coach and team, and create a CoachStudent assignment with projects
        foreach ($students as $student) {
            $team = $teams->random();
            $coach = $coaches->random();

            // Attach student to team if not already
            if (!$student->teams->contains($team->id)) {
                $student->teams()->attach($team->id);
            }

            // Create CoachStudent assignment if not exists
            $coachStudent = CoachStudent::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'coach_id' => $coach->id,
                    'team_id' => $team->id,
                ],
                [
                    'active' => true,
                ]
            );

            // Add some projects for each assignment (simulate JSON or related table as needed)
            $projects = [
                ['name' => 'Project A for '.$student->name],
                ['name' => 'Project B for '.$student->name],
            ];
            $coachStudent->projects = json_encode($projects);
            $coachStudent->save();
        }
    }
}
