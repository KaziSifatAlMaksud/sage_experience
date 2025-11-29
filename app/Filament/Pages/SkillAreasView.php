<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\SkillArea;
use App\Models\Skill;
use App\Models\Practice;
use Illuminate\Support\Facades\Auth;

class SkillAreasView extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';
    protected static string $view = 'filament.pages.skill-areas-view';
    protected static ?string $navigationLabel = 'Review Skill Practices';
    protected static ?string $navigationGroup = '';
    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'Review Skill Practices';

    public $skillAreas = [];
    public $expandedSkillArea = null;
    public $expandedSkill = null;
    public $colors = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && $user->hasRole('student');
    }

    public function mount(): void
    {
        // Load all skill areas with their skills and practices
// TODO: In the Blade view, make each skill area and skill collapsible to show/hide practices, and color-code the left border of each skill area for navigation clarity.
        $this->skillAreas = SkillArea::with(['skills.practices'])->orderBy('name')->get();

        // Set up nice colors for each area
        $colorPalette = [
            '#3B82F6', // Blue
            '#10B981', // Green
            '#F59E0B', // Amber
            '#EF4444', // Red
            '#8B5CF6', // Purple
            '#EC4899', // Pink
            '#06B6D4', // Cyan
            '#F97316', // Orange
        ];

        foreach ($this->skillAreas as $index => $area) {
            $this->colors[$area->id] = $colorPalette[$index % count($colorPalette)];
        }
    }

    public function toggleSkillArea($areaId)
    {
        if ($this->expandedSkillArea === $areaId) {
            $this->expandedSkillArea = null;
            $this->expandedSkill = null;
        } else {
            $this->expandedSkillArea = $areaId;
            $this->expandedSkill = null;
        }
    }

    public function toggleSkill($skillId)
    {
        if ($this->expandedSkill === $skillId) {
            $this->expandedSkill = null;
        } else {
            $this->expandedSkill = $skillId;
        }
    }
}
