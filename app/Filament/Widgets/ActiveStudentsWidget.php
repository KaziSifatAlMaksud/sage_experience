<?php


// app/Filament/Widgets/CurrentProjectsWidget.php
// app/Filament/Widgets/CurrentProjectsWidget.php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class CurrentStudentsWidget extends Widget
{
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return false;
    }

    public function render(): View
    {
        $students = User::role(User::ROLE_STUDENT)
            ->select('id','name', 'email', 'school') // You can add more fields if needed
            ->get()
            ->toArray();

        Log::channel('project_debug')->debug('Fetched students:', [
            'columnSpan' => $this->columnSpan,
            'students' => $students,
        ]);

        return view('filament.widgets.active-students-widget', [
            'students' => $students,
        ]);
    }
}
