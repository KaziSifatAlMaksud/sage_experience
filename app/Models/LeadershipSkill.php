<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadershipSkill extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'leadership_subgroup_id',
        'order',
    ];

    /**
     * Get the subgroup that owns this skill.
     */
    public function subgroup()
    {
        return $this->belongsTo(LeadershipSubgroup::class, 'leadership_subgroup_id');
    }

    /**
     * Get the users associated with this skill.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_has_skills', 'leadership_skill_id', 'user_id')
                    ->withPivot('course_id', 'level')
                    ->withTimestamps();
    }

    /**
     * Get the group this skill belongs to through the subgroup.
     */
    public function group()
    {
        return $this->hasOneThrough(
            LeadershipGroup::class,
            LeadershipSubgroup::class,
            'id', // Foreign key on leadership_subgroups table
            'id', // Foreign key on leadership_groups table
            'leadership_subgroup_id', // Local key on leadership_skills table
            'leadership_group_id' // Local key on leadership_subgroups table
        );
    }
}
