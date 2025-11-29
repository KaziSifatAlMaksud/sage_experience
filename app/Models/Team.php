<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'created_by',
        'status',
        'project_advisor_id',
        'subject_mentor_id',
    ];

    /**
     * Get the users that belong to the team.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }


    public function scopeStatus($query, string $status)
{
    return $query->where('status', $status);
}

    /**
     * Get active users in this team.
     */
    public function activeUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role', 'start_date', 'end_date', 'is_active')
            ->whereNull('team_user.end_date')
            ->withTimestamps();
    }

    /**
     * Get past users of this team.
     */
    public function pastUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role', 'start_date', 'end_date', 'is_active')
            ->whereNotNull('team_user.end_date')
            ->withTimestamps();
    }

    /**
     * Get the admin who created the team.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the project advisor for this team.
     */
    public function projectAdvisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'project_advisor_id');
    }

    /**
     * Get the subject mentor for this team.
     */
    public function subjectMentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subject_mentor_id');
    }

    /**
     * Get the students in this team.
     */
    public function students()
    {
        return $this->users()->role('student');
    }

    /**
     * Get the subject mentors in this team.
     */
    public function subjectMentors(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->wherePivot('role', User::ROLE_SUBJECT_MENTOR);
    }

    /**
     * Get the personal coaches in this team.
     */
    public function personalCoaches()
    {
        return $this->users()->role('personal_coach');
    }

    /**
     * Get all feedback for this team.
     */
    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    /**
     * Get the coach-student relationships in this team.
     */
    public function coachStudentRelationships()
    {
        return $this->hasMany(CoachStudent::class);
    }

    /**
     * Get the invitations for this team.
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(TeamInvitation::class);
    }
}
