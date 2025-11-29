<?php

namespace App\Filament\Resources\SkillResource\Pages;

use App\Filament\Resources\SkillResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewSkill extends ViewRecord
{
    protected static string $resource = SkillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getRecord(): \App\Models\Skill
{
    $record = parent::getRecord();

    // Attach practices with custom index field
    $record->load(['practices']);
    $record->practices->each(function ($practice, $index) {
        $practice->display_index = $index + 1;
    });

    return $record;
}

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Skill Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Skill Name')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                            ->weight('bold'),
                        Infolists\Components\TextEntry::make('skillArea.name')
                        ->icon('heroicon-o-pencil-square')
                            ->label('Skill Area')
                            ->badge()
                            ->url(fn ($record) => $record->skillArea
    ? route('filament.admin.resources.skill-areas.edit', ['record' => $record->skillArea->id])
    : null
)
                            ->color(fn ($record) => $record->skillArea?->color ?? 'gray'),
                        Infolists\Components\TextEntry::make('description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                    

                Infolists\Components\Section::make('Practices')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('practices')
                            ->schema([
                                Infolists\Components\TextEntry::make('description')
                                    ->label('Practice Description'),
                              Infolists\Components\TextEntry::make('display_index')
    ->label('Order')
    ->badge()
    ->size('sm'),  
                                    Infolists\Components\TextEntry::make('id')
    ->label('')
    ->hiddenLabel()
    ->icon('heroicon-o-pencil-square')
    ->formatStateUsing(fn () => ' ') // non-breaking space (not empty!)
    ->url(fn ($record) => route('filament.admin.resources.practices.edit', ['record' => $record->id]))
    ->tooltip('Edit Practice')
                            ])
                            ->columns(3)
                    ]) ->collapsible()





            ]);
    }
}