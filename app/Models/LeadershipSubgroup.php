<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadershipSubgroup extends Model
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
        'leadership_group_id',
        'order',
    ];

    /**
     * Get the group that owns this subgroup.
     */
    public function group()
    {
        return $this->belongsTo(LeadershipGroup::class, 'leadership_group_id');
    }

    /**
     * Get the skills associated with this subgroup.
     */
    public function skills()
    {
        return $this->hasMany(LeadershipSkill::class);
    }

    /**
     * Get the users associated with this subgroup.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_has_subgroups', 'leadership_subgroup_id', 'user_id')
                    ->withPivot('course_id')
                    ->withTimestamps();
    }
}
