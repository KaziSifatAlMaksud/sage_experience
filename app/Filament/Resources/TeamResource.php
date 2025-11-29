<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamResource\Pages;
use App\Filament\Resources\TeamResource\RelationManagers;
use App\Models\Team;
use App\Models\User;
use App\Filament\BaseResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use App\Services\CoachStudentService;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class TeamResource extends BaseResource
{
    protected static ?string $model = Team::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Projects';
    protected static ?string $pluralModelLabel = 'Projects';
    protected static ?string $modelLabel = 'Project';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Select::make('project_advisor_id')
    ->label('Project Advisor')
    ->relationship('projectAdvisor', 'name', function ($query) {
        // Show all project advisors plus the currently logged in user if they are a project advisor
        $query->role('project_advisor');
    })
    ->default(function() {
        // Set current user as default if they are a project advisor
        return auth()->user()->hasRole('project_advisor') ? auth()->id() : null;
    })
    ->searchable()
    ->preload() // Preload the data to ensure it displays properly
    ->required(),

                                Forms\Components\Select::make('subject_mentor_id')
    ->label('Subject Mentor')
    ->relationship('subjectMentor', 'name', function ($query) {
        // Show all subject mentors plus the currently logged in user if they are a subject mentor
        $query->role('subject_mentor');
    })
    ->default(function() {
        // Set current user as default if they are a subject mentor
        return auth()->user()->hasRole('subject_mentor') ? auth()->id() : null;
    })
    ->searchable()
    ->preload() // Preload the data to ensure it displays properly
    ->required(),

                                Forms\Components\TextInput::make('name')
                                    ->label('Project Name')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\Select::make('status')
                                    ->label('Project Status')
                                    ->options([
                                        'active' => 'Active',
                                        'completed' => 'Completed',
                                    ])
                                    ->default('active')
                                    ->required(),
                            ])
                            ->columns(2),

                        Forms\Components\Section::make('Students')
                            ->schema([
                                Forms\Components\Select::make('users')
                                    ->label('Students')
                                    ->relationship('users', 'name', function (Builder $query) {
                                        return $query->role('student');
                                    })
                                    ->multiple()
                                    ->preload()
                                    ->searchable(),
                            ]),

                        Forms\Components\Section::make('Student Coach Assignments')
                            ->schema([
                                Forms\Components\Repeater::make('student_coaches')
                                    ->schema([
                                        Forms\Components\Select::make('student_id')
                                            ->label('Student')
                                            ->options(function (callable $get) {
                                                // Get the selected users (students) from above
                                                $selectedUserIds = $get('../../users');
                                                if (empty($selectedUserIds)) {
                                                    return [];
                                                }

                                                return User::whereIn('id', $selectedUserIds)
                                                    ->role('student')
                                                    ->pluck('name', 'id');
                                            })
                                            ->searchable()
                                            ->required(),

                                        Forms\Components\Select::make('coach_id')
                                            ->label('Personal Coach')
                                            ->options(
                                                User::role('personal_coach')
                                                    ->pluck('name', 'id')
                                            )
                                            ->searchable()
                                            ->required(),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Project Notes')
                            ->schema([
                                Forms\Components\Placeholder::make('created_at')
                                    ->label('Date Created')
                                    ->content(fn ($record): ?string => $record?->created_at?->diffForHumans()),

                                Forms\Components\Placeholder::make('updated_at')
                                    ->label('Date Updated')
                                    ->content(fn ($record): ?string => $record?->updated_at?->diffForHumans()),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {  

        
        return $table
            ->columns([

                 Tables\Columns\TextColumn::make('name')
                    ->label('Project Name')
                    ->searchable()
                    ->sortable()
                    ->action(function ($record) {
                        return Tables\Actions\EditAction::make()
                            ->record($record)
                            ->modalHeading('Edit Project Name')
                            ->form([
                                Forms\Components\TextInput::make('name')
                                    ->label('Project Name')
                                    ->required()
                                    ->maxLength(255)
                            ])
                            ->modalWidth('md')
                            ->modalSubmitActionLabel('Save Changes');
                    }),



                    
                Tables\Columns\TextColumn::make('projectAdvisor.name')
                    ->label('Project Advisor')
                    ->searchable()
                    ->sortable()
                    ->default('Not assigned'),

                Tables\Columns\TextColumn::make('subjectMentor.name')
                    ->label('Subject Mentor')
                    ->searchable()
                    ->sortable()
                    ->default('Not assigned'),

               

       Tables\Columns\TextColumn::make('students')
    ->label('Students')
    ->html()
    ->wrap()
    ->extraAttributes([
        'style' => 'white-space: normal; line-height: 0.9; padding: 0; margin: 0;',
    ])
    ->getStateUsing(function ($record) {
        return $record->users()
            ->role('student')
            ->pluck('name')
            ->map(fn ($name) => e(Str::limit($name, 9)))
            ->implode('<br>');
    }),



                Tables\Columns\BadgeColumn::make('status')
                    ->label('Project Status')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Active',
                        'completed' => 'Completed',
                        default => $state,
                    })
                    ->colors([
                        'active' => 'success',
                        'completed' => 'secondary',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date Created')
                    ->dateTime('M j, Y')
                    ->toggleable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Date Updated')
                    ->dateTime('M j, Y')
                    ->toggleable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\UsersRelationManager::make(),
            RelationManagers\InvitationsRelationManager::make(),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'edit' => Pages\EditTeam::route('/{record}/edit'),
            'completed' => Pages\ListCompletedTeams::route('/completed'),
        ];
    }
}
