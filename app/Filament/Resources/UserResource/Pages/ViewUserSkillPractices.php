<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\Page;
use App\Models\UserSkillPractice;
use App\Models\Feedback;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ViewUserSkillPractices extends Page
{
    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.resources.user-resource.pages.view-user-skill-practices';

    protected static ?string $title = 'Student Skill Practices';

    public $record;
    public $demonstratedPractices = [];
    public $futurePractices = [];
    public $feedbackByPractice = [];

    public function mount(int | string $record)
    {
        $this->record = $this->resolveRecord($record);

        // Security check - can only be viewed by admin, mentors, coaches, or the student themselves
        abort_unless(
            auth()->user()->hasRole(['admin', 'subject_mentor', 'personal_coach']) ||
            auth()->id() === $this->record->id,
            403
        );

        $this->loadPractices();
        $this->loadFeedback();
    }

    public static function resolveRecord($key): User
{
    return User::findOrFail($key);
}

    protected function loadPractices()
    {
        // Get the student's demonstrated skills
        $this->demonstratedPractices = UserSkillPractice::with(['skillArea', 'skill', 'practice'])
            ->where('user_id', $this->record->id)
            ->where('is_demonstrated', true)
            ->orderBy('selected_at', 'desc')
            ->get();

        // Get the student's future skills to work on
        $this->futurePractices = UserSkillPractice::with(['skillArea', 'skill', 'practice'])
            ->where('user_id', $this->record->id)
            ->where('is_demonstrated', false)
            ->orderBy('selected_at', 'desc')
            ->get();
    }

    protected function loadFeedback()
    {
        // Get all feedback for this student
        $feedback = Feedback::with(['skill', 'practice', 'sender', 'team'])
            ->where('receiver_id', $this->record->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Group feedback by practice ID
        foreach ($feedback as $item) {
            if ($item->practice_id) {
                if (!isset($this->feedbackByPractice[$item->practice_id])) {
                    $this->feedbackByPractice[$item->practice_id] = [];
                }
                $this->feedbackByPractice[$item->practice_id][] = $item;
            }
        }
    }
}
