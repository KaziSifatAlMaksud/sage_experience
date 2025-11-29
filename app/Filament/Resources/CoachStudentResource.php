<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CoachStudentResource\Pages;
use App\Filament\Resources\CoachStudentResource\RelationManagers;
use App\Models\CoachStudent;
use App\Models\User;
use App\Models\Team;
use App\Filament\BaseResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class CoachStudentResource extends BaseResource
{
    protected static ?string $model = CoachStudent::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
protected static ?string $navigationLabel = 'Students';
protected static ?string $pluralModelLabel = 'Students';
protected static ?string $modelLabel = 'Student';
protected static ?string $navigationGroup = 'User Management';
protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Student')
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label('Student')
                            ->options(fn () => \App\Models\User::where('role', 'student')->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->visible(fn ($record) => !$record),
                        Forms\Components\TextInput::make('student_name')
                            ->label('Student Name')
                            ->disabled()
                            ->formatStateUsing(fn ($state, $record) => $record?->student?->name ?? '-')
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record),
                        Forms\Components\TextInput::make('student_email')
                            ->label('Student Email')
                            ->email()
                            ->required()
                            ->default(fn ($record) => $record?->student?->email ?? '')
                            ->formatStateUsing(fn ($state, $record) => $record?->student?->email ?? ''),
                        Forms\Components\TextInput::make('student_phone')
                            ->label('Student Phone/WeChat')
                            ->required()
                            ->default(fn ($record) => $record?->student?->student_phone ?? '')
                            ->formatStateUsing(fn ($state, $record) => $record?->student?->student_phone ?? ''),
                        Forms\Components\TextInput::make('student_school')
                            ->label('Student School')
                            ->required()
                            ->default(fn ($record) => $record?->student?->student_school ?? '')
                            ->formatStateUsing(fn ($state, $record) => $record?->student?->student_school ?? ''),
                        Forms\Components\TextInput::make('parent1_name')
                            ->label('Parent 1 Name')
                            ->required()
                            ->default(fn ($record) => $record?->student?->parent1_name ?? '')
                            ->formatStateUsing(fn ($state, $record) => $record?->student?->parent1_name ?? ''),
                        Forms\Components\TextInput::make('parent1_contact')
                            ->label('Parent 1 Contact')
                            ->required()
                            ->default(fn ($record) => $record?->student?->parent1_contact ?? '')
                            ->formatStateUsing(fn ($state, $record) => $record?->student?->parent1_contact ?? ''),
                        Forms\Components\TextInput::make('parent2_name')
                            ->label('Parent 2 Name')
                            ->required()
                            ->default(fn ($record) => $record?->student?->parent2_name ?? '')
                            ->formatStateUsing(fn ($state, $record) => $record?->student?->parent2_name ?? ''),
                        Forms\Components\TextInput::make('parent2_contact')
                            ->label('Parent 2 Contact')
                            ->required()
                            ->default(fn ($record) => $record?->student?->parent2_contact ?? '')
                            ->formatStateUsing(fn ($state, $record) => $record?->student?->parent2_contact ?? ''),
                    ]),
                Forms\Components\Section::make('Team Assignment')
                    ->schema([
                        Forms\Components\Select::make('team_id')
                            ->label('Team')
                            ->options(fn () => \App\Models\Team::pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                    ]),
                Forms\Components\Section::make('Projects')
                    ->schema([
                        Forms\Components\Repeater::make('projects')
                            ->label('Projects')
                            ->minItems(1)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Project Name')
                                    ->required(),
                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('edit_name')
                                        ->label('Edit Name')
                                        ->action(fn ($record, $state, $set) => $set('name', $state['name'])),
                                ]),
                            ])
                            ->columns(2),
                    ]),
                Forms\Components\Section::make('Coach Assignment')
                    ->schema([
                        Forms\Components\Select::make('coach_id')
                            ->label('Personal Coach')
                            ->options(
                                User::role('personal_coach')
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->required(),
                    ]),
            ]);
    }


    public static function canViewAny(): bool
{
    return Auth::user()?->role === 'student';
}

    public static function table(Table $table): Table
    {
        return $table
                        ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Student Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('student.teams')
                    ->label('Teams')
                    ->formatStateUsing(fn($record) => $record->student?->teams?->pluck('name')->join(', ') ?? '-')
                    ->limit(30)
                    ->tooltip(fn($record) => $record->student?->teams?->pluck('name')->join(', ') ?? '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('coach.name')
                    ->label('Personal Coach')
                    ->sortable()
                    ->searchable(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoachStudents::route('/'),
            'create' => Pages\CreateCoachStudent::route('/create'),
            'edit' => Pages\EditCoachStudent::route('/{record}/edit'),
        ];
    }
}
