<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\SkillArea;
use App\Models\Skill;
use App\Models\Practice;

class SkillSummary extends Component
{
    public $currentStrengths = [];   // Input coming from user selection
    public $Skillarray = [];         // FINAL STORED ARRAY
    public $showSummary = false;     // To show after save

    public function mount($currentStrengths)
    {
        $this->currentStrengths = $currentStrengths;
    }

    // Build the global skill array only ONCE when user completes selection
    public function buildSkillArray()
    {
        $this->Skillarray = []; // store fresh once

        foreach(array_slice($this->currentStrengths, 0, 3) as $item) {

            if(!empty($item['skill_id'])) {

                $area     = SkillArea::find($item['area_id']);
                $skill    = Skill::find($item['skill_id']);
                $practice = Practice::find($item['practice_id']);

                $baseColor = $area->color ?? "#666";
                [$r,$g,$b] = sscanf($baseColor, "#%02x%02x%02x");

                $this->Skillarray[] = [
                    'area'     => $area->name ?? null,
                    'skill'    => $skill->name ?? null,
                    'practice' => $practice->description ?? null,
                    'color' => [
                        'base' => $baseColor,
                        'mid'  => sprintf("#%02x%02x%02x", max($r-30,0), max($g-30,0), max($b-30,0)),
                        'dark' => sprintf("#%02x%02x%02x", max($r-60,0), max($g-60,0), max($b-60,0)),
                    ]
                ];
            }
        }

        $this->showSummary = true;
    }

    // If you want to save permanently to DB later
    public function saveToDatabase()
    {
        // Example store JSON
        auth()->user()->update([
            'saved_skills' => json_encode($this->Skillarray),
        ]);
    }

    public function render()
    {
        return view('livewire.skill-summary');
    }
}
