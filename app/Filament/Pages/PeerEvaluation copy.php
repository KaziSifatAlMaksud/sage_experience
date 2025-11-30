<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use App\Models\User;
use App\Models\Team;
use App\Models\SkillArea;
use App\Models\Skill;
use App\Models\Practice;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;

class PeerEvaluation extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static string $view = 'filament.pages.peer-evaluation';
    protected static ?string $navigationLabel = 'Evaluate Team Member';
    protected static ?string $navigationGroup = '';
    protected static ?int $navigationSort = 9;

    protected static ?string $title = 'Evaluate Team Member Performance';

    // For team member selection
    public $teamMembers = [];
    public $userTeams = [];
    public $selectedMemberId = null;
    public $selectedMember = null;

    // For skill selection
    public $skillAreas = [];
    public $selectedSkillArea = null;
    public $selectedSkill = null;
    public $skills = [];
    public $practices = [];
    public $colors = [];

    public bool $enableRecentTab = true;
    public bool $enableFutureTab = true;


    // For the evaluation workflow
    public $currentStep = 1; // 1 = select member, 2 = select strengths, 3 = select improvements, 4 = review
    public $currentQuestion = 1;
    public $questions = [
        1 => 'What 3 skill practices did your team member demonstrate well?',
        2 => 'What 3 skill practices could your team member improve on?'
    ];

    // For selections
    public $selectedSkillAreaId = null;
    public $selectedSkillId = null;
    public $selectedPracticeId = null;
    public $currentStrengths = [];
    public $skillsToImprove = [];
    public $maxSelections = 3;
    public $currentSkillSet = 1;

    // For feedback comments
    public $feedbackComments = '';

    /**
     * Control access to this page based on user role.
     * Only students should have access to the peer evaluation page.
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && $user->hasRole('student');
    }



    public function mount(): void
    {
        // Only show for students
        $user = Auth::user();
        if (!$user || !$user->hasRole('student')) {
            abort(403, 'This page is only available to students.');
        }

        // Load team members (peers) that share a team with the current user
        $this->loadTeamMembers();

        // Load all skill areas for the selection process
        $this->loadSkillAreas();

        $this->loadUserTeams();

        // Initialize the collections
        $this->currentStrengths = [
            1 => ['area_id' => null, 'skill_id' => null, 'practice_id' => null],
            2 => ['area_id' => null, 'skill_id' => null, 'practice_id' => null],
            3 => ['area_id' => null, 'skill_id' => null, 'practice_id' => null],
        ];

        $this->skillsToImprove = [
            1 => ['area_id' => null, 'skill_id' => null, 'practice_id' => null],
            2 => ['area_id' => null, 'skill_id' => null, 'practice_id' => null],
            3 => ['area_id' => null, 'skill_id' => null, 'practice_id' => null],
        ];

        // Ensure selectedSkillArea is always set
        $this->selectedSkillArea = null;
    }


    private function loadUserTeams(): void
{
    $this->userTeams = Auth::user()
        ->teams()
        ->orderBy('name')
        ->get();
}

   private function loadTeamMembers(): void
{
    $user = Auth::user();

    // Get the teams the current user belongs to (with members eager-loaded)
    $teams = $user->teams()->with(['users' => function ($query) use ($user) {
        $query->where('users.id', '!=', $user->id)
              ->role('student')
              ->orderBy('name');
    }])->get();

    // Flatten all users from these teams into a unique collection
    $this->teamMembers = $teams->pluck('users')->flatten()->unique('id')->values();
}

    private function loadSkillAreas(): void
    {
        // Load all skill areas for initial selection
        $this->skillAreas = SkillArea::with('skills.practices')->orderBy('name')->get();

        // Set up color palette
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

        foreach ($this->skillAreas as $index => $area) {
            $this->colors[$area->id] = $colorPalette[$index % count($colorPalette)];
        }
    }

    public function selectTeamMember($memberId)
    {
        $this->selectedMemberId = $memberId;
        $this->selectedMember = User::find($memberId);
        $this->currentStep = 2;
        $this->currentQuestion = 1;
        $this->currentSkillSet = 1;
        $this->currentStrengths = [
            1 => ['area_id' => null, 'skill_id' => null, 'practice_id' => null],
            2 => ['area_id' => null, 'skill_id' => null, 'practice_id' => null],
            3 => ['area_id' => null, 'skill_id' => null, 'practice_id' => null],
        ];
    }

    public function selectSkillArea($skillAreaId, $skillSet = null)
    {
        $skillSet = $skillSet ?? $this->currentSkillSet;

        // Validate skill area exists
        $skillArea = SkillArea::find($skillAreaId);
        if (!$skillArea) {
            Notification::make()
                ->title('Error')
                ->body('Invalid skill area selected.')
                ->danger()
                ->send();
            return;
        }

        // Reset subsequent selections in the current skill set
        if ($this->currentQuestion == 1) {
            $this->currentStrengths[$skillSet]['area_id'] = $skillAreaId;
            $this->currentStrengths[$skillSet]['skill_id'] = null;
            $this->currentStrengths[$skillSet]['practice_id'] = null;
        } else {
            $this->skillsToImprove[$skillSet]['area_id'] = $skillAreaId;
            $this->skillsToImprove[$skillSet]['skill_id'] = null;
            $this->skillsToImprove[$skillSet]['practice_id'] = null;
        }

        // Update current selection references
        $this->selectedSkillAreaId = $skillAreaId;
        $this->selectedSkillArea = $skillArea;

        // Clear previous selections
        $this->selectedSkillId = null;
        $this->selectedSkill = null;
        $this->selectedPracticeId = null;
        $this->practices = [];

        // Load skills for this area
        $this->skills = Skill::where('skill_area_id', $skillAreaId)
            ->orderBy('name')
            ->get();

        // Move to next step (select skill)
        $this->dispatch('skillAreaSelected');
    }

    public function selectSkill($skillId, $skillSet = null)
    {
        $skillSet = $skillSet ?? $this->currentSkillSet;

        // Store the selection in the appropriate skill set
        if ($this->currentQuestion == 1) {
            $this->currentStrengths[$skillSet]['skill_id'] = $skillId;
        } else {
            $this->skillsToImprove[$skillSet]['skill_id'] = $skillId;
        }

        // Update current selection references
        $this->selectedSkillId = $skillId;
        $this->selectedSkill = Skill::find($skillId);

        // Clear practice selection
        $this->selectedPracticeId = null;
        $this->practices = [];

        // Load practices for this skill
        $practices = Practice::where('skill_id', $skillId)
            ->orderBy('description')
            ->get();

        $this->practices = $practices;

        // Move to next step (select practice)
        $this->dispatch('skillSelected');
    }

    public function selectPractice($practiceId, $skillSet = null)
    {
        $skillSet = $skillSet ?? $this->currentSkillSet;

        // Store the selection in the appropriate skill set
        if ($this->currentQuestion == 1) {
            $this->currentStrengths[$skillSet]['practice_id'] = $practiceId;
        } else {
            $this->skillsToImprove[$skillSet]['practice_id'] = $practiceId;
        }

        $this->selectedPracticeId = $practiceId;
        $this->selectedPractice = Practice::find($practiceId);

        // Move to next skill set or question
        if ($skillSet < $this->maxSelections) {
            $this->currentSkillSet++;
            // Reset selections for the next skill set
            $this->selectedSkillAreaId = null;
            $this->selectedSkillId = null;
            $this->selectedPracticeId = null;
        } else {
            if ($this->currentQuestion == 1) {
                // Move to improvements
                $this->currentQuestion = 2;
                $this->currentSkillSet = 1;
                // Reset selections for the next question
                $this->selectedSkillAreaId = null;
                $this->selectedSkillId = null;
                $this->selectedPracticeId = null;
            } else {
                // Move to review
                $this->currentStep = 3;
            }
        }

        $this->dispatch('practiceSelected');
    }

    public function addFeedbackComment($comments)
    {
        $this->feedbackComments = $comments;
        $this->currentStep = 4; // Move to final review
    }

    public function submitFeedback()
    {
        $user = Auth::user();

        // Find a team that both users are in
        $userTeams = $user->teams()->pluck('teams.id');
        $memberTeams = User::find($this->selectedMemberId)->teams()->pluck('teams.id');
        $commonTeamId = $userTeams->intersect($memberTeams)->first();

        if (!$commonTeamId) {
            Notification::make()
                ->title('Error')
                ->body('You do not share a team with this member anymore.')
                ->danger()
                ->send();

            return;
        }

        // Save positive feedback
        foreach ($this->currentStrengths as $strength) {
            if (isset($strength['practice_id'])) {
                Feedback::create([
                    'sender_id' => $user->id,
                    'receiver_id' => $this->selectedMemberId,
                    'team_id' => $commonTeamId,
                    'skill_id' => $strength['skill_id'],
                    'practice_id' => $strength['practice_id'],
                    'is_positive' => true,
                    'comments' => $this->feedbackComments,
                ]);
            }
        }

        // Save needs improvement feedback
        foreach ($this->skillsToImprove as $improvement) {
            if (isset($improvement['practice_id'])) {
                Feedback::create([
                    'sender_id' => $user->id,
                    'receiver_id' => $this->selectedMemberId,
                    'team_id' => $commonTeamId,
                    'skill_id' => $improvement['skill_id'],
                    'practice_id' => $improvement['practice_id'],
                    'is_positive' => false,
                    'comments' => $this->feedbackComments,
                ]);
            }
        }

        Notification::make()
            ->title('Feedback Submitted')
            ->body('Your feedback has been submitted successfully.')
            ->success()
            ->send();

        // Reset the form
        $this->reset(['selectedMemberId', 'selectedMember', 'currentStep', 'currentQuestion',
                    'currentSkillSet', 'selectedSkillAreaId', 'selectedSkillId', 'selectedPracticeId',
                    'currentStrengths', 'skillsToImprove', 'feedbackComments']);

        $this->loadTeamMembers();
    }

    public function goBack()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }

        if ($this->currentStep == 2) {
            if ($this->currentQuestion == 2 && $this->currentSkillSet == 1) {
                // Go back to strengths
                $this->currentQuestion = 1;
                $this->currentSkillSet = count($this->currentStrengths);
            } else if ($this->currentSkillSet > 1) {
                $this->currentSkillSet--;
            }
        }
    }
}