<?php

namespace App\Filament\Resources\TeamResource\Pages;

use App\Filament\Resources\TeamResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTeam extends CreateRecord
{
    protected static string $resource = TeamResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


     protected function getFormActions(): array
    {
        return [
            // Main Create button with emerald background and white text
            Actions\CreateAction::make()
                ->label('Create')
                ->button()
                
                ->extraAttributes([
                    'class' => 'bg-white text-black m-1 border border-gray-300', // margin 4px and white text
                ]),

            // Create & Create Another with white background and black text
            Actions\Action::make('createAnother')
                ->label('Create & Create Another')
                ->action(function () {
                    $this->create(false); // Prevent redirect
                    $this->fillForm(); // Reset form
                })
                ->extraAttributes([
                    'class' => 'bg-white text-black m-1 border border-gray-300', // White bg, black text, margin
                ]),

            // Cancel button - navigates back to index
            Actions\Action::make('cancel')
    ->label('Cancel')
    ->url($this->getResource()::getUrl('index'))
    ->color('danger') // Applies red background using Tailwind
    ->button() // Make it look like a button
    ->extraAttributes([
        'class' => 'text-black bg-red-600 m-1 border border-gray-300',
    ]),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
