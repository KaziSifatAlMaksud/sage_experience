<?php


// app/Filament/Widgets/CurrentProjectsWidget.php
namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use App\Models\Team;

class CurrentProjectsWidget extends Widget
{
    protected int | string | array $columnSpan = 'full';


     public static function canView(): bool
    {
        return false;
    }


    public function render(): View
    {
          $projects = Team::status('completed')
            ->select('name', 'status') // Only select required fields
            ->get()
            ->toArray();
        
        return view('filament.widgets.current-projects-widget', [
            'projects' => $projects,
        ]);
    }
}