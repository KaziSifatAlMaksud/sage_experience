<?php

namespace App\Providers;

use App\Models\Feedback;
use App\Models\TeamInvitation;
use App\Models\User;
use App\Observers\FeedbackObserver;
use App\Observers\TeamInvitationObserver;
use App\Observers\UserObserver;
use App\Providers\Filament\FilamentThemeServiceProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Vite;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register our custom Filament theme service provider
        $this->app->register(FilamentThemeServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force Vite to use the production manifest
        if (app()->environment('production')) {
            Vite::useManifestFilename('manifest.json');
        }

        // Register the User observer
        User::observe(UserObserver::class);

        // Register the TeamInvitation observer
        TeamInvitation::observe(TeamInvitationObserver::class);

        // Register the Feedback observer
        Feedback::observe(FeedbackObserver::class);
    }
}
