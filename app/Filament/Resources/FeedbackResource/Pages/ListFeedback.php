<?php

namespace App\Filament\Resources\FeedbackResource\Pages;

use App\Filament\Resources\FeedbackResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Models\UserSkillPractice;

class ListFeedback extends ListRecords
{
    protected static string $resource = FeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();
        $user = auth()->user();

        // If the user is a student, only show feedback where they are the recipient
        if ($user->hasRole('student')) {
            // First, get practice IDs from this student's selected practices
            $practiceIds = UserSkillPractice::where('user_id', $user->id)
                ->pluck('practice_id')
                ->toArray();

            // Filter feedback to only show items related to this student as recipient
            // AND related to their selected practice IDs if they have any
            $query->where('receiver_id', $user->id);

            // If they have selected practices, also filter by those
            if (!empty($practiceIds)) {
                $query->whereIn('practice_id', $practiceIds);
            }
        }

        return $query;
    }
}
