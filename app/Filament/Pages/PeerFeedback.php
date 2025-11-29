<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Feedback;
use App\Models\User;
use App\Models\SkillArea;
use Illuminate\Support\Facades\Auth;

class PeerFeedback extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static string $view = 'filament.pages.peer-feedback';
    protected static ?string $navigationLabel = 'Peer Feedback';
    protected static ?string $navigationGroup = '';
    protected static ?int $navigationSort = 8;

    protected static ?string $title = 'Peer Feedback';

    public $feedbackFromPeers = [];
    public $feedbackToPeers = [];
    public $colors = [];
    public $totalReceived = 0;
    public $totalGiven = 0;

    /**
     * Control access to this page based on user role.
     * Only students should have access to the peer feedback page.
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && !$user->hasRole('student');
    }

    public function mount(): void
    {
        // Only show for students
        $user = Auth::user();
        if (!$user || !$user->hasRole('student')) {
            abort(403, 'This page is only available to students.');
        }

        

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

        // Get feedback received from peers
        $this->feedbackFromPeers = Feedback::with(['skill.skillArea', 'practice', 'sender', 'team'])
            ->where('receiver_id', $user->id)
            ->whereHas('sender', function($query) use ($user) {
                $query->where('users.id', '!=', $user->id)
                      ->role('student');
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('skill_id')
            ->toArray();

        $this->totalReceived = count(array_merge(...array_values($this->feedbackFromPeers)));

        // Get feedback given to peers
        $this->feedbackToPeers = Feedback::with(['skill.skillArea', 'practice', 'receiver', 'team'])
            ->where('sender_id', $user->id)
            ->whereHas('receiver', function($query) use ($user) {
                $query->where('users.id', '!=', $user->id)
                      ->role('student');
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('receiver_id')
            ->toArray();

        $this->totalGiven = count(array_merge(...array_values($this->feedbackToPeers)));
    }
}
