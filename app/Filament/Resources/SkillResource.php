<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SkillResource\Pages;
use App\Filament\Resources\SkillResource\RelationManagers;
use App\Models\Skill;
use App\Models\SkillArea;
use App\Filament\BaseResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SkillResource extends BaseResource
{
    protected static ?string $model = Skill::class;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return !($user && $user->hasRole('student'));
    }


    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Edit Skills & Practices ';
    protected static ?string $navigationGroup = 'Skills & Learning'; // You can update this if you want a new group
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('skill_area_id')
                    ->label('Skill Area')
                    ->relationship('skillArea', 'name')
                    ->preload()
                    ->required()
                    ->searchable()
                    ->placeholder('Select skill area')
                    ->noSearchResultsMessage('No skill areas found')
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\ColorPicker::make('color')
                            ->required(),
                    ]),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->extraAttributes(['class' => 'w-1/3']),
                    
                 Tables\Columns\TextColumn::make('skillArea.color')
    ->label('Skill Area')
    ->html()
    ->formatStateUsing(fn($state) => '<div style="width: 1.5rem; height: 1.5rem; background-color: ' . e($state) . '; border-radius: 0.25rem;"></div>')
    ->extraAttributes(['class' => 'w-1/3 text-center']),


    // Tables\Columns\TextColumn::make('details')
    // ->label('Details')
    // ->formatStateUsing(fn ($record) => '
    //     <div x-data="{ open: false }" class="space-y-2">
    //         <button @click="open = !open" type="button" class="text-blue-600 underline">Toggle Details</button>
    //         <div x-show="open" x-transition class="p-2 mt-2 bg-gray-50 rounded border text-sm" style="display:none;">
    //             <strong>Description:</strong> ' . e($record->description) . '<br>
    //             <strong>Created At:</strong> ' . $record->created_at->format('Y-m-d H:i:s') . '<br>
    //             <strong>Updated At:</strong> ' . $record->updated_at->format('Y-m-d H:i:s') . '
    //         </div>
    //     </div>
    // ')
    // ->html()
    // ->extraAttributes(['class' => 'w-full']),


                   Tables\Columns\TextColumn::make('practices_count')
                   ->label('Skill Practices')
                    ->searchable()
                    ->extraAttributes(['class' => 'w-1/3']),
                Tables\Columns\TextColumn::make('description')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\SelectFilter::make('skill_area')
                    ->relationship('skillArea', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]) 
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount('practices'));
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\PracticesRelationManager::make(),
        ];
    }


     public static function getSlug(): string
{
    return 'skills-practices';
}

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSkills::route('/'),
            'create' => Pages\CreateSkill::route('/create'),
            'edit' => Pages\EditSkill::route('/{record}/edit'),
            'view' => Pages\ViewSkill::route('/{record}'),
        ];
    }

    
}
