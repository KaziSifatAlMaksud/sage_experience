<?php


namespace App\Filament\Resources\TeamResource\Pages;

use App\Filament\Resources\TeamResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCompletedTeams extends ListRecords
{
    protected static string $resource = TeamResource::class;

    /**
     * Override the default query to show only completed teams.
     */
    protected function getTableQuery(): ?Builder
    {
        return parent::getTableQuery()
            ->where('status', 'completed');
    }
}
