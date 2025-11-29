<?php

namespace App\Providers\Filament;

use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\ServiceProvider;

class FilamentThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register custom scripts and styles
        FilamentAsset::register([
            Js::make('sidebar', public_path('js/sidebar.js')),
            Js::make('horizontal-sidebar', public_path('js/horizontal-sidebar-fix.js')),
            Css::make('filament-admin-theme', public_path('build/assets/theme-BB44v1dL.css')),
        ]);

        // Include horizontal sidebar scripts only when topNavigation is used
        FilamentView::registerRenderHook(
            'panels::body.end',
            fn (): string => view('filament.components.horizontal-sidebar-scripts')->render(),
        );

        // Include sidebar customization scripts
        FilamentView::registerRenderHook(
            'panels::body.end',
            fn (): string => view('filament.components.sidebar-scripts')->render(),
        );
    }
}
