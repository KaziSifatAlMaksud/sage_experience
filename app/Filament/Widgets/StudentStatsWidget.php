<?php

namespace App\Filament\Widgets;

use App\Models\Feedback;
use App\Models\Skill;
use App\Models\User;
use App\Models\UserSkillPractice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class StudentStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '60s';
    protected static ?int $sort = 5;

    public static function canView(): bool
    {
        $user = auth()->user();

        // Only students should see this widget
        return $user && !$user->hasRole('student');
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        $userId = $user->id;

        // Cache key for this user's stats
        $cacheKey = "student_stats_{$userId}";

        // Try to get from cache first
        return Cache::remember($cacheKey, 60, function() use ($user, $userId) {
            // Get the student's team information
            $teamIds = $user->teams()->pluck('teams.id')->toArray();

            $teamCount = count($teamIds);

            // Get counts of demonstrated and future practices
            $demonstratedCount = UserSkillPractice::where('user_id', $userId)
                ->where('is_demonstrated', true)
                ->count();

            $futureCount = UserSkillPractice::where('user_id', $userId)
                ->where('is_demonstrated', false)
                ->count();

            // Get mentor count
            $mentorsCount = User::role('subject_mentor')
                ->whereHas('teams', function($query) use ($teamIds) {
                    $query->whereIn('teams.id', $teamIds);
                })
                ->count();

            // Get feedback stats with optimized query
            $userFeedbackCount = Feedback::where('receiver_id', $userId)->count();

            // Only compute this expensive query if user has received feedback
            $feedbackPercentile = 0;

            if ($userFeedbackCount > 0) {
                // Use a cached value for average student feedback to reduce load
                $avgStudentFeedback = Cache::remember('avg_student_feedback', 900, function() {
                    return DB::table('feedback')
                        ->join('model_has_roles', function($join) {
                            $join->on('feedback.receiver_id', '=', 'model_has_roles.model_id')
                                 ->where('model_has_roles.model_type', '=', 'App\\Models\\User');
                        })
                        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                        ->where('roles.name', 'student')
                        ->select('feedback.receiver_id')
                        ->groupBy('feedback.receiver_id')
                        ->selectRaw('COUNT(*) as feedback_count')
                        ->get()
                        ->avg('feedback_count') ?: 0;
                });

                $feedbackPercentile = $avgStudentFeedback > 0
                    ? round(min(100, ($userFeedbackCount / $avgStudentFeedback) * 100))
                    : 0;
            }

            return [
                Stat::make('Teams', $teamCount)
                    ->description('Projects you\'re part of')
                    ->descriptionIcon('heroicon-m-user-group')
                    ->color('primary'),

                Stat::make('Feedback Received', $userFeedbackCount)
                    ->description($feedbackPercentile > 0 ? "Top {$feedbackPercentile}% of students" : 'Keep working to get feedback')
                    ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                    ->color('success'),

                Stat::make('Skills Practiced', $demonstratedCount)
                    ->description($demonstratedCount > 0 ? 'Skills you\'ve demonstrated' : 'No skills demonstrated yet')
                    ->descriptionIcon('heroicon-m-check-badge')
                    ->color('info'),

                Stat::make('Skills to Work On', $futureCount)
                    ->description($futureCount > 0 ? 'Skills to improve' : 'No skills selected to improve')
                    ->descriptionIcon('heroicon-m-academic-cap')
                    ->url(route('filament.admin.pages.skill-practice'))
                    ->color('warning'),
            ];
        });
    }

    /**
     * Return empty stats when no teams are available
     */
    private function getEmptyStats(): array
    {
        $totalSkills = Cache::remember('total_skills_count', 3600, function() {
            return Skill::count();
        });

        return [
            Stat::make('Your Feedback', 0)
                ->description('Items received')
                ->descriptionIcon('heroicon-m-inbox')
                ->color('success'),

            Stat::make("Student Percentile", "0%")
                ->description('Compared to peers')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),

            Stat::make('Top Skill', 'None')
                ->description("0 feedback items")
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('warning'),

            Stat::make('Skills With Feedback', 0)
                ->description("Out of {$totalSkills} total")
                ->descriptionIcon('heroicon-m-document-check')
                ->color('info'),

            Stat::make('Personal Coaches', 0)
                ->description('Assigned to you')
                ->descriptionIcon('heroicon-m-user-circle')
                ->color('danger'),

            Stat::make('Subject Mentors', 0)
                ->description('In your teams')
                ->descriptionIcon('heroicon-m-user')
                ->color('gray'),
        ];
    }
}
