<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeedbackResource\Pages;
use App\Filament\Resources\FeedbackResource\RelationManagers;
use App\Models\Feedback;
use App\Filament\BaseResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSkillPractice;

class FeedbackResource extends BaseResource
{
    protected static ?string $model = Feedback::class;

    


    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'Review Feedback';
    protected static ?string $navigationGroup = 'Skills & Learning';
    protected static ?int $navigationSort = 3;

   
    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if ($user && $user->hasRole('student')) {
            return false;
        }
        return true;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Feedback Details')
                    ->schema([
                        Forms\Components\Select::make('sender_id')
                            ->relationship('sender', 'name')
                            ->label('From')
                            ->default(fn() => Auth::check() ? Auth::id() : null)
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                        Forms\Components\Select::make('receiver_id')
                            ->relationship('recipient', 'name')
                            ->label('To')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set) {
                                // Reset dependent fields when recipient changes
                                $set('skill_id', null);
                                $set('practice_id', null);
                            })
                            ->placeholder('Select recipient')
                            ->noSearchResultsMessage('No users found'),
                        Forms\Components\Select::make('team_id')
                            ->relationship('team', 'name')
                            ->label('Project Team')
                            ->required()
                            ->reactive()
                            ->searchable()
                            ->preload()
                            ->placeholder('Select team')
                            ->noSearchResultsMessage('No teams found'),
                       
                    ])->columns(2),

                Forms\Components\Section::make('Skill & Practice')
                    ->schema([
                        Forms\Components\Select::make('skill_id')
                            ->relationship('skill', 'name', function (Builder $query, callable $get) {
                                // Get the selected student's ID
                                $studentId = $get('receiver_id');

                                if ($studentId) {
                                    // First, get the skill IDs from student's selected practices
                                    $studentSkillIds = UserSkillPractice::where('user_id', $studentId)
                                        ->distinct()
                                        ->pluck('skill_id')
                                        ->toArray();

                                    // Include these skills first, then include all other skills
                                    return $query->with('skillArea')
                                        ->orderByRaw("CASE WHEN id IN (" .
                                            implode(',', $studentSkillIds ?: [0]) .
                                            ") THEN 0 ELSE 1 END")
                                        ->orderBy('name');
                                }

                                return $query->with('skillArea')->orderBy('name');
                            })
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->skillArea->name})")
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('practice_id', null))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder('Select skill')
                            ->noSearchResultsMessage('No skills found'),
                        Forms\Components\Select::make('practice_id')
                            ->relationship('practice', 'description', function (Builder $query, callable $get) {
                                $skillId = $get('skill_id');
                                $studentId = $get('receiver_id');

                                if (!$skillId) {
                                    return $query->whereNull('id');
                                }

                                $query = $query->where('skill_id', $skillId);

                                // If we have a student selected, prioritize their chosen practices
                                if ($studentId) {
                                    $studentPracticeIds = UserSkillPractice::where('user_id', $studentId)
                                        ->where('skill_id', $skillId)
                                        ->pluck('practice_id')
                                        ->toArray();

                                    return $query->orderByRaw("CASE WHEN id IN (" .
                                        implode(',', $studentPracticeIds ?: [0]) .
                                        ") THEN 0 ELSE 1 END")
                                        ->orderBy('description');
                                }

                                return $query;
                            })
                            ->required()
                            ->preload()
                            ->searchable()
                            ->placeholder(function (callable $get) {
                                if ($get('skill_id')) {
                                    return 'No practices available for this skill';
                                }

                                return 'Select a practice';
                            })
                            ->searchable()
                            ->searchDebounce(500)
                            ->searchingMessage('Searching practices...')
                            ->noSearchResultsMessage('No matching practices found'),
                        Forms\Components\Textarea::make('comments')
                            ->placeholder('Provide specific details about what was observed')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isStudent = auth()->user()->hasRole('student');

        $columns = [
            Tables\Columns\TextColumn::make('sender.name')
                ->label('From')
                ->searchable()
                ->sortable(),
        ];

        // Only show recipient column if user is not a student (students only see their own feedback)
        if (!$isStudent) {
            $columns[] = Tables\Columns\TextColumn::make('recipient.name')
                ->label('To')
                ->searchable()
                ->sortable();
        }

        // Add the rest of the columns
        $columns = array_merge($columns, [
            Tables\Columns\IconColumn::make('is_positive')
                ->boolean()
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->trueColor('success')
                ->falseColor('danger')
                ->label('Positive'),
            Tables\Columns\TextColumn::make('team.name')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('skill.name')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('practice.description')
                ->limit(30)
                ->tooltip(fn (Feedback $record): ?string => $record->practice?->description)
                ->searchable(),
            Tables\Columns\TextColumn::make('comments')
                ->limit(30)
                ->tooltip(fn (Feedback $record): ?string => $record->comments)
                ->searchable(),
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable(),
        ]);

        // Determine which filters to show
        $filters = [];

        if (!$isStudent) {
            // Non-students can filter by recipient and team
            $filters[] = Tables\Filters\SelectFilter::make('receiver_id')
                ->relationship('recipient', 'name')
                ->label('Recipient');

            $filters[] = Tables\Filters\SelectFilter::make('team')
                ->relationship('team', 'name');
        }

        // Add skill filter for everyone
        $filters[] = Tables\Filters\SelectFilter::make('skill')
            ->relationship('skill', 'name');

        // Add feedback type filter for everyone
        $filters[] = Tables\Filters\TernaryFilter::make('is_positive')
            ->label('Feedback Type')
            ->placeholder('All Feedback')
            ->trueLabel('Positive Feedback')
            ->falseLabel('Areas for Improvement');

        return $table
            ->columns($columns)
            ->filters($filters)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListFeedback::route('/'),
            'create' => Pages\CreateFeedback::route('/create'),
            'edit' => Pages\EditFeedback::route('/{record}/edit'),
        ];
    }
}
