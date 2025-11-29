<?php

namespace App\Filament\Resources\CoachStudentResource\Pages;

use App\Filament\Resources\CoachStudentResource;
use App\Models\User;
use App\Models\Team;
use App\Services\CoachStudentService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;

class EditCoachStudent extends EditRecord
{
    protected static string $resource = CoachStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
           
        ];
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
            ->label('Delete Student')
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








    public function getTitle(): string
    {
        // Display static full student name at top
        return $this->record->student->name ?? 'Student';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Update the related student user
        if (isset($data['student_id'])) {
            $student = \App\Models\User::find($data['student_id']);
            if ($student) {
                $student->update([
                    'email' => $data['student_email'] ?? $student->email,
                    'student_phone' => $data['student_phone'] ?? $student->student_phone,
                    'student_school' => $data['student_school'] ?? $student->student_school,
                    'parent1_name' => $data['parent1_name'] ?? $student->parent1_name,
                    'parent1_contact' => $data['parent1_contact'] ?? $student->parent1_contact,
                    'parent2_name' => $data['parent2_name'] ?? $student->parent2_name,
                    'parent2_contact' => $data['parent2_contact'] ?? $student->parent2_contact,
                ]);
            }
        }

        // Remove these fields so they are not saved to coach_student
        unset(
            $data['student_email'],
            $data['student_phone'],
            $data['student_school'],
            $data['parent1_name'],
            $data['parent1_contact'],
            $data['parent2_name'],
            $data['parent2_contact']
        );

        return $data;
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $this->authorizeAccess();

        try {
            $data = $this->form->getState();
            $data = $this->mutateFormDataBeforeSave($data);
            
            // Get the models from the form data
            $coach = User::findOrFail($data['coach_id']);
            $student = User::findOrFail($data['student_id']);
            $team = Team::findOrFail($data['team_id']);
            $notes = $data['notes'] ?? null;

            // Use our service to update the relationship
            app(CoachStudentService::class)->assignCoachToStudent(
                $coach,
                $student,
                $team,
                $notes
            );

            // Show success notification if requested
            if ($shouldSendSavedNotification) {
                Notification::make()
                    ->title('Coach assignment updated')
                    ->success()
                    ->send();
            }

            if ($shouldRedirect) {
                $this->redirect($this->getRedirectUrl());
            }
        } catch (\Exception $e) {
            // Show a friendly error message
            Notification::make()
                ->title('Error updating assignment')
                ->body('There was a problem updating this coach assignment: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}
