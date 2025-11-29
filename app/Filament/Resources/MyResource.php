<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;

class MyStandaloneResource extends Resource
{
    protected static ?string $model = \App\Models\MyModel::class;

    protected static ?string $navigationLabel = 'My Resource';
    protected static ?string $navigationIcon = 'heroicon-o-document';
     protected static ?string $navigationGroup = 'Access Management';
    protected static ?int $navigationSort = 10;

    // ❌ Do NOT include this (or set to null)
    // protected static ?string $navigationGroup = null;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            // your form fields
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            // your table columns
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyModel::route('/'),
           
        ];
    }
}
