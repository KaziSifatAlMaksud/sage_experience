<?php

namespace App\Filament\Resources\CoachStudentResource\Pages;

use App\Filament\Resources\CoachStudentResource;
use App\Models\User;
use App\Models\Team;
use App\Models\CoachStudent;
use App\Services\CoachStudentService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateCoachStudent extends CreateRecord
{
    protected static string $resource = CoachStudentResource::class;

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

    

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Check if relationship already exists
        $existingRelationship = CoachStudent::where('coach_id', $data['coach_id'])
            ->where('student_id', $data['student_id'])
            ->where('team_id', $data['team_id'])
            ->first();

        if ($existingRelationship) {
            // Update the existing relationship
            $existingRelationship->notes = $data['notes'] ?? $existingRelationship->notes;
            $existingRelationship->active = true;
            $existingRelationship->save();

            // Show notification about update
            Notification::make()
                ->title('Coach assignment updated')
                ->body('This coach was already assigned to this student. The relationship has been updated.')
                ->success()
                ->send();

            return $existingRelationship;
        }

        try {
            // Update the related student user when creating assignment
            if (isset($data['student_id'])) {
                $studentModel = User::find($data['student_id']);
                $studentModel?->update([
                    'email' => $data['student_email'] ?? $studentModel->email,
                    'student_phone' => $data['student_phone'] ?? $studentModel->student_phone,
                    'student_school' => $data['student_school'] ?? $studentModel->student_school,
                    'parent1_name' => $data['parent1_name'] ?? $studentModel->parent1_name,
                    'parent1_contact' => $data['parent1_contact'] ?? $studentModel->parent1_contact,
                    'parent2_name' => $data['parent2_name'] ?? $studentModel->parent2_name,
                    'parent2_contact' => $data['parent2_contact'] ?? $studentModel->parent2_contact,
                ]);
            }

            // Get the models from the form data
            $coach = User::findOrFail($data['coach_id']);
            $student = User::findOrFail($data['student_id']);
            $team = Team::findOrFail($data['team_id']);
            $notes = $data['notes'] ?? null;

            // Use our service to create the relationship
            $coachStudent = app(CoachStudentService::class)->assignCoachToStudent(
                $coach,
                $student,
                $team,
                $notes
            );

            return $coachStudent;
        } catch (\Exception $e) {
            // Show a friendly error message
            Notification::make()
                ->title('Error assigning coach')
                ->body($e->getMessage())
                ->danger()
                ->send();

            // Rethrow as a more friendly exception that won't show a stack trace
            throw new \Exception($e->getMessage());
        }
    }
}






//  protected function getFormActions(): array
//     {
//         return [
//             // Main Create button with emerald background and white text
//             Actions\CreateAction::make()
//                 ->label('Create')
//                 ->button()
                
//                 ->extraAttributes([
//                     'class' => 'bg-white text-black m-1 border border-gray-300', // margin 4px and white text
//                 ]),

//             // Create & Create Another with white background and black text
//             Actions\Action::make('createAnother')
//                 ->label('Create & Create Another')
//                 ->action(function () {
//                     $this->create(false); // Prevent redirect
//                     $this->fillForm(); // Reset form
//                 })
//                 ->extraAttributes([
//                     'class' => 'bg-white text-black m-1 border border-gray-300', // White bg, black text, margin
//                 ]),

//             // Cancel button - navigates back to index
//             Actions\Action::make('cancel')
//     ->label('Cancel')
//     ->url($this->getResource()::getUrl('index'))
//     ->color('danger') // Applies red background using Tailwind
//     ->button() // Make it look like a button
//     ->extraAttributes([
//         'class' => 'text-black bg-red-600 m-1 border border-gray-300',
//     ]),
//         ];
//     }