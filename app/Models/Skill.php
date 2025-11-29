<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Skill extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'skill_area_id',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Apply a more specific scope that ensures true uniqueness by ID
        static::addGlobalScope('distinct_id', function (Builder $builder) {
            // This approach ensures we're selecting the most specific distinct records by ID
            // Use a subquery to get distinct skill IDs first
            $builder->whereIn('id', function ($query) {
                $query->select('id')
                    ->from((new static)->getTable())
                    ->groupBy('id');
            });
        });
    }

    /**
     * Get the skill area this skill belongs to.
     */
    public function skillArea(): BelongsTo
    {
        return $this->belongsTo(SkillArea::class);
    }

    /**
     * Get the practices for this skill.
     */
    public function practices(): HasMany
    {
        return $this->hasMany(Practice::class)->orderBy('order');
    }

    /**
     * Get the feedback related to this skill.
     */
    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }
}
