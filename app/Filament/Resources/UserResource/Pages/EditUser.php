<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
           
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

      protected function getFormActions(): array
{
    return [
        Action::make('save')
            ->label('Save changes')
            ->submit('save')
            ->extraAttributes([
                'style' => 'margin: 8px;',
            ]),

        Action::make('cancel')
            ->label('Cancel')
            ->url($this->getResource()::getUrl())
            ->extraAttributes([
                'style' => 'margin: 8px;',
            ]),

        DeleteAction::make('delete')
            ->label('Delete User')
            ->requiresConfirmation()
            ->modalHeading('Are you sure?')
            ->modalSubheading('This action cannot be undone.')
            ->modalButton('Yes, delete')
            ->extraAttributes([
                'class' => 'ml-auto bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg px-4 py-2',
                'style' => 'margin: 8px;',
            ]),
    ];
}
}
