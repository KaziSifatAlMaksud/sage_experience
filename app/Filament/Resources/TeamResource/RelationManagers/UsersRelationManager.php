<?php

namespace App\Filament\Resources\TeamResource\RelationManagers;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Collection;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $title = 'Team Members';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Team Member Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('pivot.role')
                    ->label('Role')
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            User::ROLE_STUDENT => 'Student',
                            User::ROLE_SUBJECT_MENTOR => 'Subject Mentor',
                            User::ROLE_PERSONAL_COACH => 'Personal Coach',
                            default => $state,
                        };
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        User::ROLE_STUDENT => 'success',
                        User::ROLE_SUBJECT_MENTOR => 'primary',
                        User::ROLE_PERSONAL_COACH => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('pivot.start_date')
                    ->label('Start Date')
                    ->date('M j, Y')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('pivot.end_date')
                    ->label('End Date')
                    ->date('M j, Y')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('pivot.is_active')
                    ->label('Active Status')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('personalCoaches.name')
                    ->label('Personal Coach')
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('member_status')
                    ->label('Student Status')
                    ->placeholder('All students')
                    ->trueLabel('Current students only')
                    ->falseLabel('Past students only')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNull('team_user.end_date'),
                        false: fn (Builder $query) => $query->whereNotNull('team_user.end_date'),
                        blank: fn (Builder $query) => $query,
                    ),
                Tables\Filters\SelectFilter::make('pivot.role')
                    ->label('Role')
                    ->options([
                        User::ROLE_STUDENT => 'Students',
                        User::ROLE_SUBJECT_MENTOR => 'Subject Mentors',
                        User::ROLE_PERSONAL_COACH => 'Personal Coaches',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->searchable(),
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
                            ->required(),
                        Forms\Components\DatePicker::make('pivot.end_date')
                            ->label('End Date')
                            ->nullable(),
                    ]),
                Tables\Actions\Action::make('viewHistory')
                    ->label('View History')
                    ->icon('heroicon-o-clock')
                    ->url(fn ($record) => route('filament.admin.resources.users.edit', [
                        'record' => $record,
                        'activeRelationManager' => 2, // Index for the StudentHistoryRelationManager
                    ]))
                    ->visible(fn ($record): bool => $record->hasRole('student'))
                    ->openUrlInNewTab(),
                Tables\Actions\DetachAction::make()
                    ->modalHeading('Remove from Project')
                    ->modalDescription('Are you sure you want to remove this student from the project? Historical data will be maintained.'),
            ])
            ->defaultSort('name', 'asc')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // No bulk actions needed if we don't need the "mark as past" functionality
                ]),
            ]);
    }
}
