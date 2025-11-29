<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Events\ServingFilament;
use Filament\Facades\Filament;

class FilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Filament::serving(function (ServingFilament $event) {
            Filament::registerStyles([
                asset('css/filament-overrides.css'), // Correct path and extension
            ]);
});
    }

    public function register(): void
    {
        //
    }
}
