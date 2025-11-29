<?php

namespace App\Services;

use App\Models\CoachStudent;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CoachStudentService
{
    /**
     * Assign a coach to a student within a team context
     *
     * @param User $coach The coach to assign
     * @param User $student The student to be coached
     * @param Team $team The team context
     * @param string|null $notes Optional notes for the relationship
     * @return CoachStudent
     */
    public function assignCoachToStudent(User $coach, User $student, Team $team, ?string $notes = null): CoachStudent
    {
        // Verify that the coach and student are on the team
        if (!$team->users()->where('users.id', $coach->id)->exists()) {
            throw new \InvalidArgumentException("Coach is not a member of the specified team.");
        }

        if (!$team->users()->where('users.id', $student->id)->exists()) {
            throw new \InvalidArgumentException("Student is not a member of the specified team.");
        }

        // Check if the coach has the correct role
        if (!$coach->isPersonalCoach()) {
            throw new \InvalidArgumentException("The assigned user must have the personal coach role.");
        }

        // Check if the student has the correct role
        if (!$student->isStudent()) {
            throw new \InvalidArgumentException("The assigned user must have the student role.");
        }

        // Check if a relationship already exists for this coach-student pair in this team
        $existingRelationship = CoachStudent::where('coach_id', $coach->id)
            ->where('student_id', $student->id)
            ->where('team_id', $team->id)
            ->first();

        if ($existingRelationship) {
            // Update the existing relationship instead of creating a new one
            $existingRelationship->notes = $notes;
            if (Schema::hasColumn('coach_student', 'active')) {
                $existingRelationship->active = true;
            }
            $existingRelationship->save();

            return $existingRelationship;
        }

        // Deactivate any existing active coach assignments for this student in this team
        if (Schema::hasColumn('coach_student', 'active')) {
            CoachStudent::where('student_id', $student->id)
                ->where('team_id', $team->id)
                ->where('active', true)
                ->update(['active' => false]);
        }

        // Create the new assignment
        $data = [
            'coach_id' => $coach->id,
            'student_id' => $student->id,
            'team_id' => $team->id,
            'notes' => $notes,
        ];

        // Add active flag if the column exists
        if (Schema::hasColumn('coach_student', 'active')) {
            $data['active'] = true;
        }

        return CoachStudent::create($data);
    }

    /**
     * Get the active coach for a student in a team
     *
     * @param User $student The student
     * @param Team $team The team context
     * @return User|null The active coach or null if none exists
     */
    public function getActiveCoach(User $student, Team $team): ?User
    {
        $activeCoachRelationship = CoachStudent::where('student_id', $student->id)
            ->where('team_id', $team->id)
            ->where('active', true)
            ->first();

        if (!$activeCoachRelationship) {
            return null;
        }

        return User::find($activeCoachRelationship->coach_id);
    }

    /**
     * Transfer a student to a new team while preserving their coaching history
     *
     * @param User $student The student to transfer
     * @param Team $sourceTeam The original team
     * @param Team $destinationTeam The target team
     * @param User|null $newCoach Optional new coach to assign in the destination team
     * @return void
     */
    public function transferStudentToTeam(User $student, Team $sourceTeam, Team $destinationTeam, ?User $newCoach = null): void
    {
        // Begin a database transaction
        DB::beginTransaction();

        try {
            // Check if student is in the source team
            if (!$sourceTeam->users()->where('users.id', $student->id)->exists()) {
                throw new \InvalidArgumentException("Student is not a member of the source team.");
            }

            // Keep the student in the source team (no end_date setting needed)
            // We simply add them to the destination team

            // Add student to the destination team if not already present
            if (!$destinationTeam->users()->where('users.id', $student->id)->exists()) {
                $destinationTeam->users()->attach($student->id, [
                    'role' => User::ROLE_STUDENT,
                    'start_date' => now(),
                ]);
            }

            // If a new coach is specified, assign them to the student in the new team
            if ($newCoach) {
                $this->assignCoachToStudent($newCoach, $student, $destinationTeam);
            }

            // Commit the transaction
            DB::commit();
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get all coaching history for a student across all teams
     *
     * @param User $student The student to get coaching history for
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStudentCoachingHistory(User $student)
    {
        return CoachStudent::with(['coach', 'team'])
            ->where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
