<?php

namespace Database\Seeders;

use App\Models\CoachStudent;
use App\Models\Feedback;
use App\Models\Practice;
use App\Models\Skill;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test users with different roles
        $this->createTestUsers();

        // Create teams
        $this->createTeams();

        // Create team invitations
        $this->createTeamInvitations();

        // Assign students to coaches
        $this->assignCoaches();

        // Create feedback
        $this->createFeedback();

        $this->command->info('Dummy data created successfully!');
    }

    /**
     * Create test users with different roles.
     */
    private function createTestUsers(): void
    {
        // Create students
        for ($i = 1; $i <= 10; $i++) {
            // Skip creation if user with this email already exists
            if (User::where('email', "student{$i}@example.com")->exists()) {
                $this->command->info("Student {$i} already exists, skipping...");
                continue;
            }

            $user = User::create([
                'name' => "Student $i",
                'email' => "student{$i}@example.com",
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => User::ROLE_STUDENT,
                'parent1_name' => "Parent1 of Student $i",
                'parent1_contact' => "+1555000{$i}01",
                'parent2_name' => "Parent2 of Student $i",
                'parent2_contact' => "+1555000{$i}02",
                'phone' => "+1555123{$i}00",
                'school' => "School $i",
            ]);

            $user->assignRole('student');
        }

        // Create project advisors
        for ($i = 1; $i <= 3; $i++) {
            if (User::where('email', "advisor{$i}@example.com")->exists()) {
                $this->command->info("Project Advisor {$i} already exists, skipping...");
                continue;
            }
            $user = User::create([
                'name' => "Project Advisor $i",
                'email' => "advisor{$i}@example.com",
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => User::ROLE_PROJECT_ADVISOR,
            ]);
            $user->assignRole('project_advisor');
        }

        // Create subject mentors
        for ($i = 1; $i <= 3; $i++) {
            // Skip creation if user with this email already exists
            if (User::where('email', "mentor{$i}@example.com")->exists()) {
                $this->command->info("Subject Mentor {$i} already exists, skipping...");
                continue;
            }

            $user = User::create([
                'name' => "Subject Mentor $i",
                'email' => "mentor{$i}@example.com",
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => User::ROLE_SUBJECT_MENTOR,
            ]);

            $user->assignRole('subject_mentor');
        }

        // Create personal coaches
        for ($i = 1; $i <= 5; $i++) {
            // Skip creation if user with this email already exists
            if (User::where('email', "coach{$i}@example.com")->exists()) {
                $this->command->info("Personal Coach {$i} already exists, skipping...");
                continue;
            }

            $user = User::create([
                'name' => "Personal Coach $i",
                'email' => "coach{$i}@example.com",
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => User::ROLE_PERSONAL_COACH,
            ]);

            $user->assignRole('personal_coach');
        }
    }

    /**
     * Create teams and add members.
     */
    private function createTeams(): void
    {
        // Get the admin user
        $admin = User::where('email', 'admin@example.com')->first();

        // Get students
        $students = User::role('student')->get();

        // Get mentors and coaches
        $mentors = User::role('subject_mentor')->get();
        $coaches = User::role('personal_coach')->get();

        // Create 3 teams
        $advisors = User::role('project_advisor')->get();
        $statuses = ['active', 'completed', 'active'];
        for ($i = 1; $i <= 3; $i++) {
            $team = Team::create([
                'name' => "Project $i",
                'description' => "This is a description for Project $i",
                'created_by' => $admin->id,
                'status' => $statuses[$i-1],
                'project_advisor_id' => $advisors->get(($i-1) % $advisors->count())?->id,
                'subject_mentor_id' => $mentors->get($i - 1)?->id,
            ]);

            // Students - assign 2 students to multiple teams for multi-project test
            $teamStudents = $students->slice(($i-1) * 3, 3);
            foreach ($teamStudents as $student) {
                try {
                    $team->users()->attach($student->id, ['role' => User::ROLE_STUDENT]);
                } catch (\Illuminate\Database\QueryException $e) {
                    // ignore duplicate pivot entries
                }
            }
            // Assign Student 1 and 2 to all teams for multi-project
            if ($i == 1) {
                $student1 = $students->get(0);
                $student2 = $students->get(1);
                if ($student1) {
                    try { $team->users()->attach($student1->id, ['role' => User::ROLE_STUDENT]); } catch (\Illuminate\Database\QueryException $e) {}
                }
                if ($student2) {
                    try { $team->users()->attach($student2->id, ['role' => User::ROLE_STUDENT]); } catch (\Illuminate\Database\QueryException $e) {}
                }
            }
            // Subject mentors - assign one per team
            $mentor = $mentors->get($i - 1);
            if ($mentor) {
                try {
                    $team->users()->attach($mentor->id, ['role' => User::ROLE_SUBJECT_MENTOR]);
                } catch (\Illuminate\Database\QueryException $e) {}
            }
            // Personal coaches - distribute across teams
            if ($i <= count($coaches)) {
                $coach = $coaches->get($i - 1);
                try {
                    $team->users()->attach($coach->id, ['role' => User::ROLE_PERSONAL_COACH]);
                } catch (\Illuminate\Database\QueryException $e) {}
            }
        }
        // Create a completed and an active project for each student
        foreach ($students as $i => $student) {
            $project = Team::create([
                'name' => "Student{$i} Completed Project",
                'description' => "Completed project for Student {$i}",
                'created_by' => $admin->id,
                'status' => 'completed',
                'project_advisor_id' => $advisors->random()?->id,
                'subject_mentor_id' => $mentors->random()?->id,
            ]);
            try {
                $project->users()->attach($student->id, ['role' => User::ROLE_STUDENT]);
            } catch (\Illuminate\Database\QueryException $e) {}
            $activeProject = Team::create([
                'name' => "Student{$i} Active Project",
                'description' => "Active project for Student {$i}",
                'created_by' => $admin->id,
                'status' => 'active',
                'project_advisor_id' => $advisors->random()?->id,
                'subject_mentor_id' => $mentors->random()?->id,
            ]);
            try {
                $activeProject->users()->attach($student->id, ['role' => User::ROLE_STUDENT]);
            } catch (\Illuminate\Database\QueryException $e) {}
        }
    }

    /**
     * Create team invitations.
     */
    private function createTeamInvitations(): void
    {
        // Get teams
        $teams = Team::all();

        // Create pending invitations for each team
        foreach ($teams as $team) {
            // Get the team creator
            $creator = $team->creator;

            // Create 2 pending invitations per team
            for ($i = 1; $i <= 2; $i++) {
                TeamInvitation::create([
                    'team_id' => $team->id,
                    'invited_by' => $creator->id,
                    'email' => "new{$team->id}user{$i}@example.com",
                    'role' => $i % 2 === 0 ? User::ROLE_STUDENT : User::ROLE_SUBJECT_MENTOR,
                    'token' => Str::random(32),
                    'expires_at' => now()->addDays(7),
                ]);
            }

            // Create 1 expired invitation
            TeamInvitation::create([
                'team_id' => $team->id,
                'invited_by' => $creator->id,
                'email' => "expired{$team->id}@example.com",
                'role' => User::ROLE_STUDENT,
                'token' => Str::random(32),
                'expires_at' => now()->subDays(3),
            ]);

            // Create 1 accepted invitation
            TeamInvitation::create([
                'team_id' => $team->id,
                'invited_by' => $creator->id,
                'email' => "accepted{$team->id}@example.com",
                'role' => User::ROLE_STUDENT,
                'token' => Str::random(32),
                'expires_at' => now()->addDays(7),
                'accepted_at' => now()->subDays(1),
            ]);
        }
    }

    /**
     * Assign coaches to students.
     */
    private function assignCoaches(): void
    {
        // Get teams
        $teams = Team::all();

        foreach ($teams as $team) {
            // Get students and coaches for this team
            $students = $team->students;
            $coaches = $team->personalCoaches;

            if ($students->isNotEmpty() && $coaches->isNotEmpty()) {
                // Assign the first coach to all students in this team
                $coach = $coaches->first();

                foreach ($students as $student) {
                    CoachStudent::create([
                        'coach_id' => $coach->id,
                        'student_id' => $student->id,
                        'team_id' => $team->id,
                    ]);
                }
            }
        }
    }

    /**
     * Create feedback entries.
     */
    private function createFeedback(): void
    {
        // Get skills and practices
        $skills = Skill::with('practices')->get();

        // Get teams
        $teams = Team::with(['students', 'personalCoaches', 'subjectMentors'])->get();

        foreach ($teams as $team) {
            $students = $team->students;
            $mentors = $team->subjectMentors;
            $coaches = $team->personalCoaches;

            // For each student
            foreach ($students as $student) {
                // Create feedback from mentors for every skill/practice
                foreach ($mentors as $mentor) {
                    foreach ($skills as $skill) {
                        foreach ($skill->practices as $practice) {
                            Feedback::create([
                                'sender_id' => $mentor->id,
                                'receiver_id' => $student->id,
                                'team_id' => $team->id,
                                'skill_id' => $skill->id,
                                'practice_id' => $practice->id,
                                'comments' => "Mentor feedback for {$student->name} on {$skill->name} / {$practice->description}",
                            ]);
                        }
                    }
                }
                // Create feedback from coaches for every skill/practice
                foreach ($coaches as $coach) {
                    foreach ($skills as $skill) {
                        foreach ($skill->practices as $practice) {
                            Feedback::create([
                                'sender_id' => $coach->id,
                                'receiver_id' => $student->id,
                                'team_id' => $team->id,
                                'skill_id' => $skill->id,
                                'practice_id' => $practice->id,
                                'comments' => "Coach feedback for {$student->name} on {$skill->name} / {$practice->description}",
                            ]);
                        }
                    }
                }
                // Create peer feedback from other students for every skill/practice
                $otherStudents = $students->where('id', '!=', $student->id);
                foreach ($otherStudents as $otherStudent) {
                    foreach ($skills as $skill) {
                        foreach ($skill->practices as $practice) {
                            Feedback::create([
                                'sender_id' => $otherStudent->id,
                                'receiver_id' => $student->id,
                                'team_id' => $team->id,
                                'skill_id' => $skill->id,
                                'practice_id' => $practice->id,
                                'comments' => "Peer feedback for {$student->name} on {$skill->name} / {$practice->description}",
                            ]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Create a feedback entry.
     */
    private function createFeedbackEntry(User $sender, User $receiver, Team $team, $skills): void
    {
        // Randomly select a skill and practice
        $skill = $skills->random();
        $practice = $skill->practices->random();

        Feedback::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'team_id' => $team->id,
            'skill_id' => $skill->id,
            'practice_id' => $practice->id,
            'comments' => "Feedback from {$sender->name} to {$receiver->name} about {$skill->name} / {$practice->description}",
        ]);
    }
}
