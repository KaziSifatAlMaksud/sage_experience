<?php

namespace App\Filament\Pages;

use App\Models\Feedback;
use App\Models\Skill;
use App\Models\SkillArea;
use App\Models\Team;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Filament\Pages\Page;

class StatsDashboard extends BaseDashboard
{

  protected static ?string $slug = 'stats-dashboard';
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

      protected static bool $shouldRegisterNavigation = true;

      
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Stats Dashboard';
   

    public function getTitle(): string
    {
         if (auth()->user()->hasRole('student')) {
        return "" ;
    }
        return 'Stats Dashboard';
    }


      public function getView(): string
    {
        $user = auth()->user();

        if ($user && $user->hasRole('student')) {
            return 'filament.pages.custom-dashboard';
        }

        // Return default view for other roles or fallback
        return parent::getView();
    }

   

    public static function getNavigationLabel(): string
{
     $user = auth()->user();
    return $user && $user->hasRole('student') ? 'Dashboard' : 'Stats Dashboard';
}



public static function getNavigationIcon(): ?string
{
    $user = auth()->user();
    return $user && $user->hasRole('student') ? 'heroicon-o-home' : 'heroicon-o-chart-bar';
}





    // protected function getHeaderActions(): array
    // {
    //     $actions = [];

    //     if (auth()->user()->hasRole('student')) {
    //         $actions[] = Action::make('evaluate_performance')
    //             ->label('Evaluate My Latest Performance')
    //             ->url(route('filament.admin.pages.skill-practice'))
    //             ->icon('heroicon-m-academic-cap')
    //             ->color('primary')
    //             ->extraAttributes(['class' => 'w-full md:w-28']);

    //         $actions[] = Action::make('evaluate_team_member')
    //             ->label('Evaluate Team Member Performance')
    //             ->url(route('filament.admin.pages.peer-evaluation'))
    //             ->icon('heroicon-m-user-group')
    //             ->color('primary')
    //             ->extraAttributes(['class' => 'w-full md:w-28']);

    //          $actions[] = Action::make('evaluate_team_member')
    //             ->label('Review Past Evaluations')
                
    //             ->icon('heroicon-m-user-group')
    //             ->color('primary')
    //             ->extraAttributes(['class' => 'w-full md:w-28']);
    //     }
    //     //  else if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('project_advisor')) {
    //     //     $actions[] = Action::make('create_team')
    //     //         ->label('Create Project Team')
    //     //         ->url(route('filament.admin.resources.teams.create'))
    //     //         ->icon('heroicon-m-user-group')
    //     //         ->color('primary');
    //     // }

    //     return $actions;
    // }

    protected function getHeaderWidgets(): array
    {

         if (auth()->user()->hasRole('student')) {
        return []; // No footer widgets for students
    }
        return [
            // CurrentStrengthsWidget::class,
            // SkillsToPracticeWidget::class,
           
        ];
    }

    protected function getFooterWidgets(): array
    {


         if (auth()->user()->hasRole('student')) {
        return []; // No header widgets for students
    }

        $widgets = [
            // \App\Filament\Widgets\StatsOverviewWidget::class,
        ];

        // if (auth()->user()->hasRole('student')) {
        //     $widgets[] = \App\Filament\Widgets\StudentStatsWidget::class;
        // }

        // if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('teacher') || auth()->user()->hasRole('project_advisor')) {
        //     $widgets[] = \App\Filament\Widgets\TeamStatsWidget::class;
        //     $widgets[] = \App\Filament\Widgets\UserStatsWidget::class;
        // }

        return $widgets;
    }
}



class CurrentStrengthsWidget extends BaseWidget
{

    
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $user = Auth::user();

        // Get skills with positive feedback
        $skillsWithPositiveFeedback = Skill::whereHas('practices.feedback', function ($query) use ($user) {
            $query->where('recipient_id', $user->id)
                ->where('is_positive', true);
        })
        ->withCount(['practices as feedback_count' => function ($query) use ($user) {
            $query->whereHas('feedback', function ($subQuery) use ($user) {
                $subQuery->where('recipient_id', $user->id)
                    ->where('is_positive', true);
            });
        }])
        ->orderBy('feedback_count', 'desc')
        ->with('skillArea')
        ->take(3)
        ->get();

        return $skillsWithPositiveFeedback->map(function ($skill) {
            return Stat::make($skill->name)
                ->description('From ' . $skill->skillArea->name . ' area')
                ->value($skill->feedback_count . ' positive feedback')
                ->color('success')
                ->chart([1, 2, 3, $skill->feedback_count]);
        })->toArray();
    }
}




class SkillsToPracticeWidget extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $user = Auth::user();

        // Get skills with negative or no feedback
        $skillsToPractice = Skill::whereDoesntHave('practices.feedback', function ($query) use ($user) {
            $query->where('recipient_id', $user->id)
                ->where('is_positive', true);
        })
        ->orWhereHas('practices.feedback', function ($query) use ($user) {
            $query->where('recipient_id', $user->id)
                ->where('is_positive', false);
        })
        ->withCount(['practices as feedback_count' => function ($query) use ($user) {
            $query->whereHas('feedback', function ($subQuery) use ($user) {
                $subQuery->where('recipient_id', $user->id);
            });
        }])
        ->with('skillArea')
        ->take(3)
        ->get();

        return $skillsToPractice->map(function ($skill) {
            return Stat::make($skill->name)
                ->description('From ' . $skill->skillArea->name . ' area')
                ->value('Needs practice')
                ->color('primary')
                ->url(route('filament.admin.resources.skills.view', ['record' => $skill->id]));
        })->toArray();
    }
}
