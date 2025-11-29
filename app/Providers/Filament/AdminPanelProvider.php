<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
           
            ->id('admin')
            ->path('admin')
          ->homeUrl('/admin/user-insights')
           
            ->colors([
                'primary' => [
                    50 => '240 247 240', // Lighter Sage green
                    100 => '220 235 220',
                    200 => '190 215 190',
                    300 => '150 189 150',
                    400 => '110 156 110',
                    500 => '80 129 80',  // Main Sage green
                    600 => '60 101 60',
                    700 => '50 81 50',
                    800 => '40 66 40',
                    900 => '30 56 30',
                    950 => '20 30 20',
                ],
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                 \App\Filament\Pages\UserInsights::class,
               
                
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            // ->topNavigation() // Commented out to use sidebar instead of top navigation
            ->maxContentWidth('full') // Expands content to fit full width
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                'auth',
                'admin.access',
            ])
            ->authGuard('web')
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->sidebarCollapsibleOnDesktop()
            ->brandName('Sage Experience')
            ->darkMode(false)
            ->sidebarFullyCollapsibleOnDesktop(false)
            ->navigationGroups([
                'Skills & Learning',
                'User Management',
                'Content Management',
                'Reports',
                'System',
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->colors([
                'danger' => Color::Red,
                'gray' => Color::Slate,
                'info' => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ]);
    }
}
