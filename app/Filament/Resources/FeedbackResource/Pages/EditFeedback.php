<?php

namespace App\Filament\Resources\FeedbackResource\Pages;

use App\Filament\Resources\FeedbackResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;


class EditFeedback extends EditRecord
{
    protected static string $resource = FeedbackResource::class;

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
            ->label('Delete Feedback')
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


    
    protected function afterSaved(): void
    {
        // Display a notification to the user who edited the feedback
        Notification::make()
            ->success()
            ->title('Feedback Updated')
            ->body('The feedback has been successfully updated')
            ->send();
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ensure the model is marked as modified so observers fire properly
        $this->record->forceFill([
            'comments' => $data['comments'],
           
        ]);
        
        return $data;
    }
}
