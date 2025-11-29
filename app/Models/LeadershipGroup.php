<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadershipGroup extends Model
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
        'order',
    ];

    /**
     * Get the subgroups associated with this leadership group.
     */
    public function subgroups()
    {
        return $this->hasMany(LeadershipSubgroup::class);
    }

    /**
     * Get the users associated with this leadership group.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_has_groups', 'leadership_group_id', 'user_id')
                    ->withPivot('course_id')
                    ->withTimestamps();
    }
}
