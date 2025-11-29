<?php

namespace App\Filament\Resources\SkillResource\Pages;

use App\Filament\Resources\SkillResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListSkills extends ListRecords
{
    protected static string $resource = SkillResource::class;

     protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Actions\Action::make('newPractice')
                ->label('New practice')
                ->url(route('filament.admin.resources.practices.create')), // Ensure this route is defined correctly
                
                // ->color('success'), // ❌ Removed to use default color

             Actions\Action::make('newSkillArea')
                ->label('New skill area')
                ->url(route('filament.admin.resources.practices.create'))
        ];
    }
}
