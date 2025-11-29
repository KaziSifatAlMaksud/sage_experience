<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /**
     * Scope a query to only include users with a given Spatie role.
     */
    public function scopeRole($query, $role)
    {
        return $query->whereHas('roles', function ($q) use ($role) {
            $q->where('name', $role);
        });
    }

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    // Role constants
    const ROLE_ADMIN = 'admin';
    const ROLE_STUDENT = 'student';
    const ROLE_SUBJECT_MENTOR = 'subject_mentor';
    const ROLE_PERSONAL_COACH = 'personal_coach';
    const ROLE_PROJECT_ADVISOR = 'project_advisor';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'google_id',
        'facebook_id',
        'parent1_name',
        'parent1_contact',
        'parent2_name',
        'parent2_contact',
        'phone',
        'school',
        'student_phone',
        'student_school',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the teams the user belongs to.
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)
            ->withPivot('role', 'start_date', 'end_date', 'is_active')
            ->withTimestamps();
    }

    /**
     * Get active teams the user currently belongs to.
     */
    public function activeTeams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)
            ->withPivot('role', 'start_date', 'end_date', 'is_active')
            ->whereNull('team_user.end_date')
            ->withTimestamps();
    }

    /**
     * Get past teams the user belonged to.
     */
    public function pastTeams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)
            ->withPivot('role', 'start_date', 'end_date', 'is_active')
            ->whereNotNull('team_user.end_date')
            ->withTimestamps();
    }

    /**
     * Get teams created by this user (as admin).
     */
    public function createdTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'created_by');
    }

    /**
     * Get students coached by this user (if user is a coach).
     */
    public function coachingStudents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'coach_student', 'coach_id', 'student_id')
            ->withPivot('team_id', 'notes', 'active', 'deleted_at')
            ->withTimestamps();
    }

    /**
     * Get the personal coach of this user (if user is a student).
     */
    public function personalCoaches(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'coach_student', 'student_id', 'coach_id')
            ->withPivot('team_id', 'notes', 'active', 'deleted_at')
            ->withTimestamps();
    }

    /**
     * Get feedback sent by this user.
     */
    public function sentFeedback(): HasMany
    {
        return $this->hasMany(Feedback::class, 'sender_id');
    }

    /**
     * Get feedback received by this user.
     */
    public function receivedFeedback(): HasMany
    {
        return $this->hasMany(Feedback::class, 'receiver_id');
    }

    /**
     * Get invitations sent by this user.
     */
    public function sentInvitations(): HasMany
    {
        return $this->hasMany(TeamInvitation::class, 'invited_by');
    }

    /**
     * Check if user has an admin role.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is a student.
     */
    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }

    /**
     * Check if user is a subject mentor.
     */
    public function isSubjectMentor(): bool
    {
        return $this->hasRole('subject_mentor');
    }

    /**
     * Check if user is a personal coach.
     */
    public function isPersonalCoach(): bool
    {
        return $this->hasRole('personal_coach');
    }

    /**
     * Check if user is a project advisor.
     */
    public function isProjectAdvisor(): bool
    {
        return $this->hasRole('project_advisor');
    }

    /**
     * Get the skill practices that the student has selected
     */
    public function skillPractices()
    {
        return $this->hasMany(UserSkillPractice::class);
    }

    /**
     * Get the demonstrated skill practices
     */
    public function demonstratedPractices()
    {
        return $this->hasMany(UserSkillPractice::class)
            ->where('is_demonstrated', true)
            ->latest('selected_at');
    }

    /**
     * Get the future skill practices to work on
     */
    public function futurePractices()
    {
        return $this->hasMany(UserSkillPractice::class)
            ->where('is_demonstrated', false)
            ->latest('selected_at');
    }

    /**
     * Get the leadership groups that this user belongs to.
     */
    public function leadershipGroups()
    {
        return $this->belongsToMany(LeadershipGroup::class, 'user_has_groups', 'user_id', 'leadership_group_id')
                    ->withPivot('course_id')
                    ->withTimestamps();
    }

    /**
     * Get the leadership subgroups that this user belongs to.
     */
    public function leadershipSubgroups()
    {
        return $this->belongsToMany(LeadershipSubgroup::class, 'user_has_subgroups', 'user_id', 'leadership_subgroup_id')
                    ->withPivot('course_id')
                    ->withTimestamps();
    }

    /**
     * Get the leadership skills that this user has.
     */
    public function leadershipSkills()
    {
        return $this->belongsToMany(LeadershipSkill::class, 'user_has_skills', 'user_id', 'leadership_skill_id')
                    ->withPivot('course_id')
                    ->withTimestamps();
    }
}
