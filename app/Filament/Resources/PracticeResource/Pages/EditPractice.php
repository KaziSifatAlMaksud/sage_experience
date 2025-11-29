<?php

namespace App\Filament\Resources\PracticeResource\Pages;

use App\Filament\Resources\PracticeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;

class EditPractice extends EditRecord
{
    protected static string $resource = PracticeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($record) {
                    if ($record->feedback()->count() > 0) {
                        // Abort the deletion with a notification
                        Notification::make()
                            ->danger()
                            ->title('Unable to Delete')
                            ->body('This practice cannot be deleted because it has associated feedback records.')
                            ->send();

                        $this->halt();
                    }
                }),
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
                ->label('Delete Practice')
                ->requiresConfirmation()
                ->modalHeading('Are you sure?')
                ->modalSubheading('This action cannot be undone.')
                ->modalButton('Yes, delete')
                ->extraAttributes([
                    'class' => 'ml-auto bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg px-4 py-2',
                    'style' => 'margin: 8px;',
                ])
                ->before(function ($record) {
                    // Example condition: prevent delete if user has related feedback
                    if ($record->feedback()->count() > 0) {
                        Notification::make()
                            ->danger()
                            ->title('Unable to Delete')
                            ->body('This user cannot be deleted because they have associated feedback records.')
                            ->send();

                        $this->halt();
                    }
                }),
        ];
    }
}
