<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserHasGroup extends Pivot
{
    protected $table = 'user_has_groups';

    protected $fillable = [
        'user_id',
        'leadership_group_id',
        'course_id',
        'created_at',
        'updated_at',
    ];
}
