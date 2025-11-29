<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserHasSkill extends Pivot
{
    protected $table = 'user_has_skills';

    protected $fillable = [
        'user_id',
        'leadership_skill_id',
        'course_id',
        'created_at',
        'updated_at',
    ];
}
