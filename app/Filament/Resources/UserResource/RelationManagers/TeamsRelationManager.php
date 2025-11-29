<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\User;
use App\Services\CoachStudentService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Carbon\Carbon;

class TeamsRelationManager extends RelationManager
{
    protected static string $relationship = 'teams';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('pivot.start_date')
                    ->label('Start Date')
                    ->default(now()),
                Forms\Components\DatePicker::make('pivot.end_date')
                    ->label('End Date')
                    ->nullable(),
                Forms\Components\Toggle::make('pivot.is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pivot.role')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'student' => 'success',
                        'subject_mentor' => 'warning',
                        'personal_coach' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('pivot.start_date')
                    ->label('Joined')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pivot.end_date')
                    ->label('Left')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('current_status')
                    ->label('Current')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->getStateUsing(fn ($record) => is_null($record->pivot->end_date)),
                Tables\Columns\TextColumn::make('current_coach')
                    ->label('Current Coach')
                    ->getStateUsing(function ($record) {
                        $user = $this->getOwnerRecord();
                        if ($user->isStudent()) {
                            $coach = app(CoachStudentService::class)->getActiveCoach($user, $record);
                            return $coach ? $coach->name : 'Not assigned';
                        }
                        return '-';
                    })
                    ->visible(fn () => $this->getOwnerRecord()->isStudent()),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('team_status')
                    ->label('Team Status')
                    ->placeholder('All teams')
                    ->trueLabel('Current teams only')
                    ->falseLabel('Past teams only')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNull('team_user.end_date'),
                        false: fn (Builder $query) => $query->whereNotNull('team_user.end_date'),
                        blank: fn (Builder $query) => $query,
                    ),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\Select::make('pivot.role')
                            ->label('Role')
                            ->options([
                                User::ROLE_STUDENT => 'Student',
                                User::ROLE_SUBJECT_MENTOR => 'Subject Mentor',
                                User::ROLE_PERSONAL_COACH => 'Personal Coach',
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('pivot.start_date')
                            ->label('Start Date')
                            ->default(now())
                            ->required(),
                        Forms\Components\DatePicker::make('pivot.end_date')
                            ->label('End Date')
                            ->nullable(),
                        Forms\Components\Toggle::make('pivot.is_active')
                            ->label('Active')
                            ->default(true),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form(fn (Tables\Actions\EditAction $action): array => [
                        Forms\Components\DatePicker::make('pivot.start_date')
                            ->label('Start Date')
                            ->required(),
                        Forms\Components\DatePicker::make('pivot.end_date')
                            ->label('End Date')
                            ->nullable(),
                    ]),
            ])
            ->defaultSort('team_user.start_date', 'desc')
            ->bulkActions([]);
    }
}
