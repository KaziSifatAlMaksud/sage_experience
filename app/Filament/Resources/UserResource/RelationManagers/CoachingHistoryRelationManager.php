<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\CoachStudent;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CoachingHistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'personalCoaches';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = 'Coaching History';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query, User $owningRecord) {
                // Only show this for students
                if (!$owningRecord->isStudent()) {
                    $query->whereNull('users.id');
                    return;
                }

                // Custom query to get coaching history from the pivot table
                return CoachStudent::query()
                    ->where('student_id', $owningRecord->id)
                    ->with(['coach', 'team']);
            })
            ->columns([
                Tables\Columns\TextColumn::make('coach.name')
                    ->label('Coach')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('team.name')
                    ->label('Team')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('active')
                    ->boolean()
                    ->label('Current')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Assigned On')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('team_id')
                    ->label('Team')
                    ->options(fn () => \App\Models\Team::pluck('name', 'id'))
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) {
                            return $query;
                        }

                        return $query->where('team_id', $data['value']);
                    }),
                Tables\Filters\TernaryFilter::make('active')
                    ->label('Current Coach Only')
                    ->placeholder('All coaching relationships')
                    ->trueLabel('Current coaches only')
                    ->falseLabel('Previous coaches only')
                    ->queries(
                        true: fn (Builder $query) => $query->where('coach_student.active', true),
                        false: fn (Builder $query) => $query->where('coach_student.active', false),
                        blank: fn (Builder $query) => $query,
                    ),
            ])
            ->headerActions([
                // No create action
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // No bulk actions
                ]),
            ])
            ->emptyStateHeading('No coaching history')
            ->emptyStateDescription('This user has no coaching relationships.')
            ->defaultSort('created_at', 'desc');
    }
}
