<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Feedback;
use App\Models\User;
use App\Models\Team;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class StudentHistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'teams';

    protected static ?string $title = 'Student Progress & History';

    protected static ?string $recordTitleAttribute = 'name';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->isStudent();
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Team')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pivot.start_date')
                    ->label('Joined')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('coaches')
                    ->label('Coaches')
                    ->getStateUsing(function ($record) {
                        $user = $this->getOwnerRecord();
                        $coaches = DB::table('coach_student')
                            ->join('users', 'users.id', '=', 'coach_student.coach_id')
                            ->where('student_id', $user->id)
                            ->where('team_id', $record->id)
                            ->select('users.name', 'coach_student.active')
                            ->get();

                        if ($coaches->isEmpty()) {
                            return 'No coaches';
                        }

                        $coachList = [];
                        foreach ($coaches as $coach) {
                            $status = $coach->active ? ' (Current)' : '';
                            $coachList[] = $coach->name . $status;
                        }

                        return implode(', ', $coachList);
                    }),
                Tables\Columns\TextColumn::make('feedback_count')
                    ->label('Feedback Received')
                    ->getStateUsing(function ($record) {
                        $user = $this->getOwnerRecord();
                        return Feedback::where('receiver_id', $user->id)
                            ->where('team_id', $record->id)
                            ->count();
                    })
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('contributions')
                    ->label('Contributions')
                    ->getStateUsing(function ($record) {
                        $user = $this->getOwnerRecord();
                        // This would ideally connect to your contributions tracking system
                        // For now, we'll return a placeholder
                        return 'View details';
                    })
                    ->url(fn ($record) => route('filament.admin.resources.teams.edit', ['record' => $record]))
                    ->color('primary'),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([
                Tables\Actions\Action::make('viewFeedback')
                    ->label('View Feedback')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->url(function ($record) {
                        $user = $this->getOwnerRecord();
                        // This should link to a filtered view of the feedback for this student in this team
                        // Adjust the route as needed for your application
                        return route('filament.admin.resources.feedback.index', [
                            'tableFilters[receiver_id][value]' => $user->id,
                            'tableFilters[team_id][value]' => $record->id,
                        ]);
                    })
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('viewProgress')
                    ->label('View Progress')
                    ->icon('heroicon-o-chart-bar')
                    ->url(function ($record) {
                        $user = $this->getOwnerRecord();
                        // This should link to a progress view for this student in this team
                        // Adjust the route as needed for your application
                        return route('filament.admin.resources.teams.edit', [
                            'record' => $record,
                            'activeRelationManager' => 5, // Adjust based on your relation manager index
                        ]);
                    })
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([])
            ->emptyStateHeading('No Team History')
            ->emptyStateDescription('This student has not been assigned to any teams yet.')
            ->defaultSort('team_user.start_date', 'desc');
    }
}
