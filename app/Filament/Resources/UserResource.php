<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\BaseResource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Filament\Resources\UserResource\RelationManagers\TeamsRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\CoachingHistoryRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\StudentHistoryRelationManager;
use App\Filament\Resources\UserResource\Pages\ViewUserSkillPractices;

class UserResource extends BaseResource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Users';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->maxLength(255),
                Forms\Components\Select::make('roles')
                    ->label('Role')
                    ->preload()
                    ->relationship(
                        'roles',
                        'name',
                        fn (Builder $query) => $query->orderByRaw("CASE
                            WHEN name = 'project_advisor' THEN 1
                            WHEN name = 'admin' THEN 2
                            WHEN name = 'student' THEN 3
                            WHEN name = 'subject_mentor' THEN 4
                            WHEN name = 'personal_coach' THEN 5
                            ELSE 6 END")->orderBy('name')
                    )
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\TextInput::make('guard_name')
                            ->default('web')
                            ->required(),
                    ]),
                Forms\Components\Toggle::make('status')
                    ->label('Active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'success',
                        'student' => 'primary',
                        'subject_mentor' => 'warning',
                        'personal_coach' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'Project Advisor',
                        'student' => 'Student',
                        'subject_mentor' => 'Subject Mentor',
                        'personal_coach' => 'Personal Coach',
                        default => $state,
                    }),
                Tables\Columns\ToggleColumn::make('status')
                    ->label('Active')
                    ->onColor('success')
                    ->offColor('danger'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('viewSkillPractices')
                    ->label('Skill Practices')
                    ->icon('heroicon-o-academic-cap')
                    ->color('primary')
                    ->url(fn (User $record): string => static::getUrl('view-skill-practices', ['record' => $record]))
                    ->visible(fn (User $record): bool => $record->hasRole('student')),
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
            TeamsRelationManager::class,
            CoachingHistoryRelationManager::class,
            StudentHistoryRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view-skill-practices' => ViewUserSkillPractices::route('/{record}/skill-practices'),
        ];
    }

    /**
     * Add custom action buttons to the student users
     */
    public static function getTableActions(): array
    {
        return [
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
            Tables\Actions\Action::make('viewSkillPractices')
                ->label('Skill Practices')
                ->icon('heroicon-o-academic-cap')
                ->color('primary')
                ->url(fn (User $record): string => static::getUrl('view-skill-practices', ['record' => $record]))
                ->visible(fn (User $record): bool => $record->hasRole('student')),
        ];
    }
}
