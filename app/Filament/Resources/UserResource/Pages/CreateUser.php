<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


    
      protected function getFormSchema(): array
    {
        // Optional override; fallback to UserResource::form() if not set
        return UserResource::form($this->form);
    }



        protected function getFormActions(): array
{
    return [
        Actions\Action::make('createAnother')
            ->label('Create User')
            ->action(function () {
                $this->create(false); // Prevent redirect
                $this->fillForm(); // Reset form
            })
            ->extraAttributes([
                'class' => 'bg-white text-black m-1 border border-gray-300',
            ]),

        Actions\Action::make('cancel')
            ->label('Cancel')
            ->url($this->getResource()::getUrl('index'))
            ->color('danger')
            ->button()
            ->extraAttributes([
                'class' => 'text-black bg-red-600 m-1 border border-gray-300',
            ]),
    ];
}

    protected function handleRecordCreation(array $data): Model
    {
        // Check if the email already exists
        $emailExists = DB::table('users')->where('email', $data['email'])->exists();
        if ($emailExists) {
            $this->halt();

            Notification::make()
                ->danger()
                ->title('Email Already Exists')
                ->body('A user with this email address already exists in the system.')
                ->send();

            $this->form->fill($data);
            $this->form->addError('email', 'This email is already in use.');

            return new ($this->getModel())();
        }

        return parent::handleRecordCreation($data);
    }
}
