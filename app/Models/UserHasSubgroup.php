<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserHasSubgroup extends Pivot
{
    protected $table = 'user_has_subgroups';

    protected $fillable = [
        'user_id',
        'leadership_subgroup_id',
        'course_id',
        'created_at',
        'updated_at',
    ];
}
