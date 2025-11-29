<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\UserSkillPractice;
use App\Models\Feedback;
use App\Models\SkillArea;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentFeedbackDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.pages.student-feedback-dashboard';
    protected static ?string $navigationLabel = 'My Feedback';
    protected static ?string $navigationGroup = '';
    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'My Feedback';

    public $userSkillPractices = [];
    public $userFuturePractices = [];
    public $feedbackFromCoach = [];
    public $feedbackFromPeers = [];
    public $skillAreaCount = [];
    public $totalDemonstrated = 0;
    public $totalToImprove = 0;
    public $colors = [];
    public $totalFeedback = 0;
    public $positiveFeedback = 0;
    public $improvementFeedback = 0;

    public $skillFrequency = [];
    public $improvementFrequency = [];

    /**
     * Control access to this page based on user role.
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && $user->hasRole('student');
    }

    public function mount(): void
    {
        $user = Auth::user();
        if ($user) {
            // Load demonstrated skills (is_demonstrated = true)
            $this->loadDemonstratedSkills($user);

            // Load future skills to work on (is_demonstrated = false)
            $this->loadSkillsToImprove($user);

            // Get feedback from coach
            $this->loadCoachFeedback($user);

            // Get feedback from peers
            $this->loadPeerFeedback($user);

            // Calculate skill frequencies for display
            $this->calculateSkillFrequencies();

            // Prepare color palette for skill areas
            $this->prepareColorPalette();

            // Calculate feedback stats for dashboard
            $allFeedback = $this->feedbackFromCoach->merge($this->feedbackFromPeers);
            $this->totalFeedback = $allFeedback->count();
            $this->positiveFeedback = $allFeedback->where('is_positive', true)->count();
            $this->improvementFeedback = $allFeedback->where('is_positive', false)->count();
        }
    }

    private function loadDemonstratedSkills($user)
    {
        $this->userSkillPractices = UserSkillPractice::with(['skill.skillArea', 'practice'])
            ->where('user_id', $user->id)
            ->where('is_demonstrated', true)
            ->orderBy('selected_at', 'desc')
            ->get();

        $this->totalDemonstrated = $this->userSkillPractices->count();
    }

    private function loadSkillsToImprove($user)
    {
        $this->userFuturePractices = UserSkillPractice::with(['skill.skillArea', 'practice'])
            ->where('user_id', $user->id)
            ->where('is_demonstrated', false)
            ->orderBy('selected_at', 'desc')
            ->get();

        $this->totalToImprove = $this->userFuturePractices->count();
    }

    private function loadCoachFeedback($user)
    {
        $this->feedbackFromCoach = Feedback::with(['skill.skillArea', 'practice', 'sender'])
            ->where('receiver_id', $user->id)
            ->whereHas('sender', function($query) {
                $query->role(['personal_coach', 'subject_mentor']);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function loadPeerFeedback($user)
    {
        $this->feedbackFromPeers = Feedback::with(['skill.skillArea', 'practice', 'sender'])
            ->where('receiver_id', $user->id)
            ->whereHas('sender', function($query) use ($user) {
                $query->where('users.id', '!=', $user->id)
                      ->role('student');
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function calculateSkillFrequencies()
    {
        // Calculate skill demonstration frequency
        $demonstrated = $this->userSkillPractices->groupBy('skill_id');
        foreach ($demonstrated as $skillId => $items) {
            $this->skillFrequency[$skillId] = $items->count();
        }

        // Calculate skills to improve frequency
        $toImprove = $this->userFuturePractices->groupBy('skill_id');
        foreach ($toImprove as $skillId => $items) {
            $this->improvementFrequency[$skillId] = $items->count();
        }
    }

    private function prepareColorPalette()
    {
        // Load all skill areas for color coding
        $skillAreas = SkillArea::orderBy('name')->get();

        // Set up nice colors for each area
        $colorPalette = [
            '#3B82F6', // Blue
            '#10B981', // Green
            '#F59E0B', // Amber
            '#EF4444', // Red
            '#8B5CF6', // Purple
            '#EC4899', // Pink
            '#06B6D4', // Cyan
            '#F97316', // Orange
        ];

        foreach ($skillAreas as $index => $area) {
            $this->colors[$area->id] = $colorPalette[$index % count($colorPalette)];
        }
    }
}
