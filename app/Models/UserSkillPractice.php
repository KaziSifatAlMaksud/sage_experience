<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSkillPractice extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_skill_practices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'skill_area_id',
        'skill_id',
        'practice_id',
        'selection_number',
        'selected_at',
        'is_demonstrated',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'selected_at' => 'datetime',
    ];

    /**
     * Get the user that owns this skill practice.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the skill area for this practice.
     */
    public function skillArea(): BelongsTo
    {
        return $this->belongsTo(SkillArea::class);
    }

    /**
     * Get the skill for this practice.
     */
    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    /**
     * Get the practice for this selection.
     */
    public function practice(): BelongsTo
    {
        return $this->belongsTo(Practice::class);
    }
}
