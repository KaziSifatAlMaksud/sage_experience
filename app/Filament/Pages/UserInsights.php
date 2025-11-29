<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;

use Illuminate\Support\Facades\Log;



class UserInsights extends Page
{
    protected static ?string $navigationLabel = 'Home';

   
  protected static ?string $slug = '';
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.user-insights';

    protected static ?string $title = 'Dashboard';

      public static function shouldRegisterNavigation(): bool
    {

       
        // Hide page if authenticated user has the "user" role
        $user = auth()->user();
        Log::channel('project_debug')->info('User role checked for navigation:', [
    'user_id' => $user?->id,
    'roles' => $user?->getRoleNames()->toArray(),
]);
       return !($user && $user->hasRole('student'));
    }



    public static function canAccess(): bool
{
    $user = auth()->user();

   

    // Deny access to students
    return !($user && $user->hasRole('student'));
}


    public function getHeaderActions(): array
    {
        return [
            // Uncomment and customize actions if needed
            // Action::make('view_all_users')
            //     ->label('View All Users')
            //     ->url(route('filament.admin.resources.users.index'))
            //     ->icon('heroicon-o-users')
            //     ->color('primary'),

            // Action::make('view_feedback')
            //     ->label('Feedback Reports')
            //     ->url(route('filament.admin.resources.feedback.index'))
            //     ->icon('heroicon-o-document-text')
            //     ->color('secondary'),
        ];
    }
}
