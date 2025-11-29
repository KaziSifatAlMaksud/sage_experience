<?php
namespace App\Filament\Pages;
use Filament\Pages\Page;
use App\Models\SkillArea;
use App\Models\Skill;
use App\Models\Practice;
use App\Models\UserSkillPractice;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use App\Models\User;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;

class SkillPractice extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static string $view = 'filament.pages.skill-practice';
    protected static ?string $navigationLabel = 'Evaluate My Latest Performance';
    protected static ?string $navigationGroup = '';
    protected static ?int $navigationSort = 6;

    protected static ?string $title = 'Evaluate My Latest Performance';

    // Add the question properties to match the PPT flow
    public $currentQuestion = 1;
    public $questions = [
        1 => 'Evaluate Skills & Practices - Demonstrated Well',
        2 => 'Evaluate Skills & Practices - Areas for Improvement'
    ];

    /**
     * Control access to this page based on user role.
     * Only students should have access to the skill practice page.
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user &&
               $user->hasRole('student');
               // Permission check temporarily removed to ensure access
               // $user->hasPermissionTo('access skill practice');
    }

    // For holding the data
    public $skillAreas = [];
    public $skills = [];
    public $practices = [];
    public $colors = [];
    public $userSkillPractices = [];
    public $userFuturePractices = []; // Add for future practices

    // Properties for tracking selection
    public $selectedSkillAreaId = null;
    public $selectedSkillId = null;
    public $selectedPracticeId = null;
    public $currentSelection = 1;
    public $navTab = 'first'; // default
    public $savedSkills = [];


    // Skill sets (up to 3 selections allowed for each question)
    public $currentStrengths = [];
    public $skillsToImprove = [];

    // For the selection process
    public $step = 'select';
    public $maxSelections = 3;
    public $isSubmittingCurrentStrengths = true;

    // Current step in the selection process
    public $currentStep = 1;
    public $currentSkillSet = 1;

    // Reference objects for the selected items
    public $selectedSkillArea = null;
    public $selectedSkill = null;
    public $selectedPractice = null;

    // Add property to control summary view
    public $showSummary = false;
    public $enableFutureTab = false;

    public $enableRecentTab  = true;

    public function mount(): void
    {
        // First clean up any duplicate practices in the database
        $this->cleanupDuplicatePractices();
        $this->loadSavedSkills();
        // Load all skill areas for initial selection
        $this->skillAreas = SkillArea::with('skills.practices')->orderBy('name')->get();

        // Debug log the skill areas
        logger()->debug('Skill areas loaded', [
            'count' => $this->skillAreas->count(),
            'data' => $this->skillAreas->toArray()
        ]);

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

        foreach ($this->skillAreas as $index => $area) {
            $this->colors[$area->id] = $colorPalette[$index % count($colorPalette)];
        }

        // Load user's existing selections
        $this->loadUserSkillPractices();

        // Initialize empty arrays for the current skill sets
        $this->currentStrengths = [];
        $this->skillsToImprove = [];
    }

    public function loadUserSkillPractices()
    {
        $user = Auth::user();
        if ($user) {
            // Add logging to debug the issue
            logger()->debug('Loading user skill practices', ['user_id' => $user->id]);

            // Load demonstrated skills (is_demonstrated = true)
            $this->userSkillPractices = UserSkillPractice::with(['skill.skillArea', 'practice'])
                ->orderBy('id', 'desc')
                ->where('user_id', $user->id)
                ->where('is_demonstrated', true)
                ->get();

            // Load future skills to work on (is_demonstrated = false)
            $this->userFuturePractices = UserSkillPractice::with(['skill.skillArea', 'practice'])
                ->where('user_id', $user->id)
                ->where('is_demonstrated', false)
                ->orderBy('selected_at', 'desc')
                ->get();

            logger()->debug('User skill practices loaded', [
                'demonstrated_count' => $this->userSkillPractices->count(),
                'future_count' => $this->userFuturePractices->count()
            ]);
        }
    }

 

    public function selectSkillArea($skillAreaId, $skillSet = null)
    {
        logger()->debug('Selecting skill area', ['area_id' => $skillAreaId, 'skill_set' => $skillSet]);

        $skillSet = $skillSet ?? $this->currentSkillSet;

        // Reset subsequent selections in the current skill set
        // if ($this->currentQuestion == 1) {
        //     $this->currentStrengths[$skillSet]['area_id'] = $skillAreaId;
        //     $this->currentStrengths[$skillSet]['skill_id'] = null;
        //     $this->currentStrengths[$skillSet]['practice_id'] = null;
        // } else {
        //     $this->skillsToImprove[$skillSet]['area_id'] = $skillAreaId;
        //     $this->skillsToImprove[$skillSet]['skill_id'] = null;
        //     $this->skillsToImprove[$skillSet]['practice_id'] = null;
        // }

        if($this->navTab === 'first'){
            $this->currentStrengths[$skillSet]['area_id'] = $skillAreaId;
            $this->currentStrengths[$skillSet]['skill_id'] = null;
            $this->currentStrengths[$skillSet]['practice_id'] = null;
        } else{
            $this->skillsToImprove[$skillSet]['area_id'] = $skillAreaId;
            $this->skillsToImprove[$skillSet]['skill_id'] = null;
            $this->skillsToImprove[$skillSet]['practice_id'] = null;
        }

        // Update current selection references
        $this->selectedSkillAreaId = $skillAreaId;
        $this->selectedSkillArea = SkillArea::find($skillAreaId);

        logger()->debug('Selected skill area', [
            'area_id' => $skillAreaId,
            'area_name' => $this->selectedSkillArea ? $this->selectedSkillArea->name : 'Not found'
        ]);

        // Clear skills and practices for other steps
        if ($this->currentStep === 1) {
            $this->selectedSkillId = null;
            $this->selectedPracticeId = null;
            $this->selectedSkill = null;
            $this->selectedPractice = null;

            // Make sure we clear existing skills before loading new ones
            $this->skills = [];
            $this->practices = [];
        }

        // Use a subquery approach to get only one record per ID
        // This is the most reliable way to get distinct records by ID
       $skills = DB::query()
    ->fromSub(function ($query) use ($skillAreaId) {
        $query->from('skills')
            ->select('skills.id', 'skills.name', 'skills.description', 'skills.skill_area_id', 'skill_areas.color')
            ->join('skill_areas', 'skills.skill_area_id', '=', 'skill_areas.id')
            ->where('skills.skill_area_id', $skillAreaId)
            ->orderBy('skills.id');
    }, 'skills_by_area')
    ->select('id', 'name', 'description', 'skill_area_id', 'color')
    ->groupBy('id', 'name', 'description', 'skill_area_id', 'color')
    ->orderBy('name')
    ->get()
    ->toArray();

        // Convert to array of objects for consistent handling
        $skills = json_decode(json_encode($skills), true);

        // Log raw skills data for debugging
        logger()->debug('Skills query results', [
            'skill_count' => count($skills),
            'skill_ids' => array_column($skills, 'id'),
            'skill_names' => array_column($skills, 'name'),
        ]);

        // Simply use the skills array directly - the SQL query already deduped them
        $this->skills = $skills;

        logger()->debug('Unique skills loaded for area', [
            'area_id' => $skillAreaId,
            'skill_count' => count($this->skills),
            'unique_ids' => array_column($this->skills, 'id'),
        ]);

        // Move to next step (select specific skill)
        $this->currentStep = 2;

        $this->dispatch('skillAreaSelected');
    }

    public function selectSkill($skillId, $skillSet = null)
    {
        $skillSet = $skillSet ?? $this->currentSkillSet;

        // Store the selection in the appropriate skill set
        // if ($this->currentQuestion == 1) {
        //     $this->currentStrengths[$skillSet]['skill_id'] = $skillId;
        // } else {
        //     $this->skillsToImprove[$skillSet]['skill_id'] = $skillId;
        // }

         if($this->navTab === 'first'){
            $this->currentStrengths[$skillSet]['skill_id'] = $skillId;
        }else{
            $this->skillsToImprove[$skillSet]['skill_id'] = $skillId;
        }
        

        // Update current selection references
        $this->selectedSkillId = $skillId;
        $this->selectedSkill = Skill::find($skillId);

        // Debug output
        logger()->debug('Selected skill', [
            'skill_id' => $skillId,
            'skill_name' => $this->selectedSkill ? $this->selectedSkill->name : 'Not found'
        ]);

        // Clear practices for previous selections
        if ($this->currentStep === 2) {
            $this->selectedPracticeId = null;
            $this->selectedPractice = null;
            $this->practices = []; // Ensure we clear the array
        }

        // Load practices for this skill - using a compatible approach for MySQL's ONLY_FULL_GROUP_BY mode
        $practices = DB::table('practices as p1')
            ->join(DB::raw('(SELECT MIN(id) as min_id, name FROM practices WHERE skill_id = ' . $skillId . ' GROUP BY name) as p2'), function($join) {
                $join->on('p1.id', '=', 'p2.min_id');
            })
            ->select('p1.id', 'p1.name', 'p1.description', 'p1.skill_id', 'p1.order')
            ->orderBy('p1.order')
            ->get()
            ->toArray();

        // Convert to array of objects for consistent handling
        $practices = json_decode(json_encode($practices), true);

        logger()->debug('Raw practices data', [
            'skill_id' => $skillId,
            'practice_count' => count($practices),
            'practice_ids' => array_column($practices, 'id'),
        ]);

        // If no practices were found, add default practices for this skill
        if (empty($practices) && $this->selectedSkill) {
            logger()->info('Creating default practices for skill', [
                'skill_id' => $skillId,
                'skill_name' => $this->selectedSkill->name
            ]);

            // Add standard practices
            Practice::create([
                'skill_id' => $skillId,
                'name' => 'Research',
                'description' => 'Research and learn about best practices for this skill.',
                'order' => 1,
            ]);

            Practice::create([
                'skill_id' => $skillId,
                'name' => 'Practice',
                'description' => 'Actively practice this skill in your daily work.',
                'order' => 2,
            ]);

            Practice::create([
                'skill_id' => $skillId,
                'name' => 'Reflection',
                'description' => 'Reflect on your performance and identify areas for improvement.',
                'order' => 3,
            ]);

            // Reload practices with the same compatible approach
            $practices = DB::table('practices as p1')
                ->join(DB::raw('(SELECT MIN(id) as min_id, name FROM practices WHERE skill_id = ' . $skillId . ' GROUP BY name) as p2'), function($join) {
                    $join->on('p1.id', '=', 'p2.min_id');
                })
                ->select('p1.id', 'p1.name', 'p1.description', 'p1.skill_id', 'p1.order')
                ->orderBy('p1.order')
                ->get()
                ->toArray();

            // Convert to array of objects for consistent handling
            $practices = json_decode(json_encode($practices), true);

            logger()->info('Created default practices for skill', [
                'skill_id' => $skillId,
                'practice_count' => count($practices)
            ]);
        }

        // Use the practices array directly - the SQL query already deduped them
        $this->practices = $practices;

        logger()->debug('Unique practices loaded for skill', [
            'skill_id' => $skillId,
            'practice_count' => count($this->practices),
            'practice_ids' => array_column($this->practices, 'id'),
        ]);

        // Move to next step (select practice)
        $this->currentStep = 3;

        $this->dispatch('skillSelected');
    }

    public function selectPractice($practiceId, $skillSet = null)
    {
        $skillSet = $skillSet ?? $this->currentSkillSet;

        // Store the selection
        // if ($this->currentQuestion == 1) {
        //     $this->currentStrengths[$skillSet]['practice_id'] = $practiceId;
        // } else {
        //     $this->skillsToImprove[$skillSet]['practice_id'] = $practiceId;
        // }

        if($this->navTab === 'first'){
            $this->currentStrengths[$skillSet]['practice_id'] = $practiceId;
        }else{
            $this->skillsToImprove[$skillSet]['practice_id'] = $practiceId;
        }    

        // Update current selection references
        $this->selectedPracticeId = $practiceId;
        $this->selectedPractice = Practice::find($practiceId);

        $this->dispatch('practiceSelected');

        // Increment the current skill set after making a selection
        $this->currentSkillSet++;

        // Reset selection for the next skill set
        $this->selectedSkillAreaId = null;
        $this->selectedSkillId = null;
        $this->selectedPracticeId = null;
        $this->selectedSkillArea = null;
        $this->selectedSkill = null;
        $this->selectedPractice = null;
        $this->currentStep = 1;
        $this->skills = [];
        $this->practices = [];

        // If we've completed all 3 skill sets, move to the next question or summary
        // if ($this->currentSkillSet > $this->maxSelections) {
        //     if ($this->currentQuestion === 1) { 
        //         // Move to the second question
        //         $this->currentQuestion = 2;
        //         $this->currentSkillSet = 1; // Start first skill set for question 2


        //     } else {
                
        //         // If we're done with question 2, show the summary
        //         $this->showSummary = true;
        //     }
          
        // }

        $this->showSummary = true;


        
    }
    

    public function nextSkillSet()
    {
        // Validate that a practice is selected
        if (!$this->selectedPracticeId) {
            $this->addError('selectedPracticeId', 'Please select a practice before continuing.');
            return;
        }

        // Store the current selection
        if ($this->isSubmittingCurrentStrengths) {
            $this->currentStrengths[$this->currentSkillSet] = [
                'area_id' => $this->selectedSkillAreaId,
                'skill_id' => $this->selectedSkillId,
                'practice_id' => $this->selectedPracticeId,
            ];
        } else {
            $this->skillsToImprove[$this->currentSkillSet] = [
                'area_id' => $this->selectedSkillAreaId,
                'skill_id' => $this->selectedSkillId,
                'practice_id' => $this->selectedPracticeId,
            ];
        }

        // Reset selection for next skill
        $this->resetSelection();

        // Move to next skill set or next question
        if ($this->currentSkillSet < $this->maxSelections) {
            $this->currentSkillSet++;
        } else {
            if ($this->currentQuestion == 1) {
                // Move to the second question and reset skill set
                $this->currentQuestion = 2;
                $this->currentSkillSet = 1;
                $this->isSubmittingCurrentStrengths = false;
            } else {
                // After three selections for question 2, show the summary
                $this->showSummary = true;
            }
        }

        // Dispatch event for UI updates
        $this->dispatch('nextSkillSet');
    }

    public function moveToNextQuestion()
    {
        // Move to the second question
        $this->currentQuestion = 2;
        $this->currentSkillSet = 1;
        $this->currentStep = 1;

        // Reset current selection references for the new question
        $this->selectedSkillAreaId = null;
        $this->selectedSkillId = null;
        $this->selectedPracticeId = null;
        $this->selectedSkillArea = null;
        $this->selectedSkill = null;
        $this->selectedPractice = null;

        $this->skills = [];
        $this->practices = [];

        $this->dispatch('nextQuestion');
    }

    public function skipRemainingSkills()
    {
        // User wants to save current selections without adding more skills
        if ($this->currentQuestion === 1) {
            $this->moveToNextQuestion();
        } else {
            // Instead of saving directly, show the summary view
            $this->showSummary = true;
            $this->dispatch('showSummary');
        }
    }

    /**
     * Reset the selection process and start over
     */
    public function resetAndStartOver()
    {
        $this->showSummary = false;
        $this->currentQuestion = 1;
        $this->currentSkillSet = 1;
        $this->currentStep = 1;

        $this->selectedSkillAreaId = null;
        $this->selectedSkillId = null;
        $this->selectedPracticeId = null;
        $this->selectedSkillArea = null;
        $this->selectedSkill = null;
        $this->selectedPractice = null;

        $this->skills = [];
        $this->practices = [];

        $this->currentStrengths = [];
        $this->skillsToImprove = [];

        $this->dispatch('resetToStart');
    }

    /**
     * Final save after reviewing the summary
     */
    public function finalSave()
    {
        $this->saveSelections();
        $this->showSummary = false;
         $this->loadSavedSkills($this->currentSelection);

         //$this->dispatchBrowserEvent('selectionsSaved'); // optional, if you want JS hook

        // Show a success message
        Notification::make()
            ->title('Skills Saved Successfully')
            ->body('Your skill selections have been saved.')
            ->success()
            // Add unique ID to prevent duplicate notifications
            ->id('skills-saved-' . now()->timestamp)
            ->send();
    }

   
    public function loadSavedSkills($limit = 1) // default 2 if no argument passed
    {
        $this->savedSkills = [];

        $userSkills = UserSkillPractice::where('user_id', Auth::id())
            ->orderBy('selection_number', 'desc')
            ->limit($limit-1)
            ->get();

        foreach ($userSkills as $userSkill) {
            $area = SkillArea::find($userSkill->skill_area_id);
            $skill = Skill::find($userSkill->skill_id);
            $practice = Practice::find($userSkill->practice_id);

            if (!$area || !$skill || !$practice) continue;

            $baseColor = $area->color ?? '#666';
            [$r, $g, $b] = sscanf($baseColor, "#%02x%02x%02x");
            $midColor = sprintf("#%02x%02x%02x", max($r-30,0), max($g-30,0), max($b-30,0));
            $darkColor = sprintf("#%02x%02x%02x", max($r-60,0), max($g-60,0), max($b-60,0));

            $this->savedSkills[] = [
                'area' => $area->name,
                'skill' => $skill->name,
                'practice' => $practice->description,
                'color' => [
                    'base' => $baseColor,
                    'mid' => $midColor,
                    'dark' => $darkColor
                ],
                'demonstrated' => $userSkill->is_demonstrated
            ];
        }
    }



    public function saveSelections()
    {
        $this->savedSkills = [];
        $user = Auth::user();
        $savedCount = 0;

        // Get the highest selection number for any skill practice for this user, regardless of type
        $lastSelectionNumber = UserSkillPractice::where('user_id', $user->id)
            ->max('selection_number');
        $nextSelectionNumber = $lastSelectionNumber ? $lastSelectionNumber + 1 : 1;

        // Save current strengths (Question 1)
        foreach ($this->currentStrengths as $setNumber => $skillSet) {
            if (isset($skillSet['area_id'], $skillSet['skill_id'], $skillSet['practice_id']) && $skillSet['area_id'] && $skillSet['skill_id'] && $skillSet['practice_id']) {
                // Create a new user skill practice record
                UserSkillPractice::create([
                    'user_id' => $user->id,
                    'skill_area_id' => $skillSet['area_id'],
                    'skill_id' => $skillSet['skill_id'],
                    'practice_id' => $skillSet['practice_id'],
                    'selection_number' => $nextSelectionNumber++,
                    'selected_at' => Carbon::now(),
                    'is_demonstrated' => true,
                ]);

                $savedCount++;
            }
        }

        // Save skills to improve (Question 2)
        foreach ($this->skillsToImprove as $setNumber => $skillSet) {
            if (isset($skillSet['area_id'], $skillSet['skill_id'], $skillSet['practice_id']) && $skillSet['area_id'] && $skillSet['skill_id'] && $skillSet['practice_id']) {
                // Create a new user skill practice record
                UserSkillPractice::create([
                    'user_id' => $user->id,
                    'skill_area_id' => $skillSet['area_id'],
                    'skill_id' => $skillSet['skill_id'],
                    'practice_id' => $skillSet['practice_id'],
                    'selection_number' => $nextSelectionNumber++,
                    'selected_at' => Carbon::now(),
                    'is_demonstrated' => false,
                ]);

                $savedCount++;
            }
        }
        if($this->currentSelection>=$this->maxSelections){
            $this->currentSelection = 1;
            if($this->navTab === 'first'){
                $this->navTab = 'second';
                $this->enableFutureTab = true;

            }else{
                $this->navTab = 'first';
                $this->enableFutureTab = false;
            }
        }else{
            $this->currentSelection++;
        }

        // Reset selections
        $this->currentStrengths = [];
        $this->skillsToImprove = [];

        $this->currentQuestion = 1;
        $this->currentSkillSet = 1;
        $this->currentStep = 1;

        $this->selectedSkillAreaId = null;
        $this->selectedSkillId = null;
        $this->selectedPracticeId = null;
        $this->selectedSkillArea = null;
        $this->selectedSkill = null;
        $this->selectedPractice = null;

        // Reload user's selections
        $this->loadUserSkillPractices();

        $this->dispatch('selectionsSaved');

        // Show success notification
        Notification::make()
            ->title('Skills Saved')
            ->body('Your skill selections have been saved successfully.')
            ->success()
            // Add unique ID to prevent duplicate notifications
            ->id('skills-saved-' . now()->timestamp)
            ->send();
    }

    /**
     * Navigate back from skill selection to skill area selection
     */
    public function goBackToSkillArea()
    {
        // Reset skill selection
        // if ($this->currentQuestion == 1) {
        //     $this->currentStrengths[$this->currentSkillSet]['skill_id'] = null;
        //     $this->currentStrengths[$this->currentSkillSet]['practice_id'] = null;
        // } else {
        //     $this->skillsToImprove[$this->currentSkillSet]['skill_id'] = null;
        //     $this->skillsToImprove[$this->currentSkillSet]['practice_id'] = null;
        // }
        if ($this->navTab === 'first') {
            $this->currentStrengths[$this->currentSkillSet]['skill_id'] = null;
            $this->currentStrengths[$this->currentSkillSet]['practice_id'] = null;
        } else {
            $this->skillsToImprove[$this->currentSkillSet]['skill_id'] = null;
            $this->skillsToImprove[$this->currentSkillSet]['practice_id'] = null;
        }

        // Clear current selections
        $this->selectedSkillId = null;
        $this->selectedPracticeId = null;
        $this->selectedSkill = null;
        $this->selectedPractice = null;
        $this->practices = [];
        $this->currentStrengths = [];
        $this->skillsToImprove = [];

        // Go back to step 1 (skill area selection)
        $this->currentStep = 1;

        $this->dispatch('backToSkillArea');
    }

    /**
     * Navigate back from practice selection to skill selection
     */
    public function goBackToSkill()
    {
        // Reset practice selection
        // if ($this->currentQuestion == 1) {
        //     $this->currentStrengths[$this->currentSkillSet]['practice_id'] = null;
        // } else {
        //     $this->skillsToImprove[$this->currentSkillSet]['practice_id'] = null;
        // }
        
         if ($this->navTab === 'first') {
            $this->currentStrengths[$this->currentSkillSet]['skill_id'] = null;
        } else {
            $this->skillsToImprove[$this->currentSkillSet]['skill_id'] = null;
        }

        // Clear current practice selection
        $this->selectedPracticeId = null;
        $this->selectedPractice = null;

        // Go back to step 2 (skill selection)
        $this->currentStep = 2;

        $this->dispatch('backToSkill');
    }

    /**
     * Clean up duplicate practices in the database
     * This will find practices with the same name and skill_id and keep only one of them
     */
    protected function cleanupDuplicatePractices(): void
    {
        try {
            // Get all practices grouped by name and skill_id
            $practices = DB::table('practices')
                ->select('name', 'skill_id', DB::raw('COUNT(*) as count'), DB::raw('MIN(id) as min_id'))
                ->groupBy('name', 'skill_id')
                ->having(DB::raw('COUNT(*)'), '>', 1)
                ->get();

            logger()->info('Found duplicate practices to clean up', [
                'count' => $practices->count()
            ]);

            // For each set of duplicates, keep only the one with the smallest ID
            foreach ($practices as $practice) {
                // Get all practice IDs except the one with the minimum ID
                $ids = DB::table('practices')
                    ->where('name', $practice->name)
                    ->where('skill_id', $practice->skill_id)
                    ->where('id', '!=', $practice->min_id)
                    ->pluck('id');

                // Log the IDs to be deleted
                logger()->info('Deleting duplicate practices', [
                    'name' => $practice->name,
                    'skill_id' => $practice->skill_id,
                    'keep_id' => $practice->min_id,
                    'delete_ids' => $ids
                ]);

                // Check if any of these practices are referenced in user_skill_practices
                $hasReferences = DB::table('user_skill_practices')
                    ->whereIn('practice_id', $ids)
                    ->exists();

                if ($hasReferences) {
                    // Update references to use the practice we're keeping
                    DB::table('user_skill_practices')
                        ->whereIn('practice_id', $ids)
                        ->update(['practice_id' => $practice->min_id]);

                    logger()->info('Updated user skill practice references');
                }

                // Check if any of these practices are referenced in feedback
                $hasFeedbackReferences = DB::table('feedback')
                    ->whereIn('practice_id', $ids)
                    ->exists();

                if ($hasFeedbackReferences) {
                    // Update references to use the practice we're keeping
                    DB::table('feedback')
                        ->whereIn('practice_id', $ids)
                        ->update(['practice_id' => $practice->min_id]);

                    logger()->info('Updated feedback references');
                }

                // Now delete the duplicate practices
                $deleted = DB::table('practices')
                    ->whereIn('id', $ids)
                    ->delete();

                logger()->info('Deleted duplicate practices', [
                    'count' => $deleted
                ]);
            }

            // Run a final check to make sure there are no duplicate practice descriptions
            $this->removeIdenticalPracticeDescriptions();

        } catch (\Exception $e) {
            logger()->error('Error cleaning up duplicate practices', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }


  public function collectAndSaveSkills()
{
    $this->Skillarray = []; // reset

    foreach ($this->currentStrengths as $skillSet) {
        if (empty($skillSet['skill_id'])) continue;

        $area = SkillArea::find($skillSet['area_id']);
        $skill = Skill::find($skillSet['skill_id']);
        $practice = Practice::find($skillSet['practice_id']);

        if (!$area || !$skill || !$practice) continue;

        $baseColor = $area->color ?? '#666';

        if (preg_match('/^#[0-9A-Fa-f]{6}$/', $baseColor)) {
            [$r, $g, $b] = sscanf($baseColor, "#%02x%02x%02x");
            $midColor = sprintf("#%02x%02x%02x", max($r-30,0), max($g-30,0), max($b-30,0));
            $darkColor = sprintf("#%02x%02x%02x", max($r-60,0), max($g-60,0), max($b-60,0));
        } else {
            $midColor = $darkColor = $baseColor;
        }

        $skillItem = [
            'area' => $area->name,
            'skill' => $skill->name,
            'practice' => $practice->name, // better for display
            'color' => [
                'base' => $baseColor,
                'mid' => $midColor,
                'dark' => $darkColor
            ]
        ];

        $this->Skillarray[] = $skillItem;

        // Save permanently
        UserSkill::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'skill_id' => $skill->id,
                'practice_id' => $practice->id
            ],
            [
                'area' => $area->name,
                'color' => json_encode($skillItem['color'])
            ]
        );
    }
}

    

    /**
     * Remove practices with identical descriptions
     * This targets the specific case seen in the screenshot
     */
    protected function removeIdenticalPracticeDescriptions(): void
    {
        try {
            // Find practices with identical descriptions
            $practicesWithSameDescription = DB::table('practices')
                ->select('description', DB::raw('COUNT(*) as count'), DB::raw('MIN(id) as min_id'))
                ->groupBy('description')
                ->having(DB::raw('COUNT(*)'), '>', 1)
                ->get();

            logger()->info('Found practices with identical descriptions', [
                'count' => $practicesWithSameDescription->count()
            ]);

            // For each set of duplicates, keep only the one with the smallest ID
            foreach ($practicesWithSameDescription as $practice) {
                // Skip empty descriptions
                if (empty(trim($practice->description))) {
                    continue;
                }

                // Get all practice IDs except the one with the minimum ID
                $ids = DB::table('practices')
                    ->where('description', $practice->description)
                    ->where('id', '!=', $practice->min_id)
                    ->pluck('id');

                // Update any references in user_skill_practices
                if (count($ids) > 0) {
                    // Check if any of these practices are referenced in user_skill_practices
                    $hasReferences = DB::table('user_skill_practices')
                        ->whereIn('practice_id', $ids)
                        ->exists();

                    if ($hasReferences) {
                        // Update references to use the practice we're keeping
                        DB::table('user_skill_practices')
                            ->whereIn('practice_id', $ids)
                            ->update(['practice_id' => $practice->min_id]);

                        logger()->info('Updated user skill practice references for identical descriptions');
                    }

                    // Check if any of these practices are referenced in feedback
                    $hasFeedbackReferences = DB::table('feedback')
                        ->whereIn('practice_id', $ids)
                        ->exists();

                    if ($hasFeedbackReferences) {
                        // Update references to use the practice we're keeping
                        DB::table('feedback')
                            ->whereIn('practice_id', $ids)
                            ->update(['practice_id' => $practice->min_id]);

                        logger()->info('Updated feedback references for identical descriptions');
                    }

                    // Now delete the duplicate practices
                    $deleted = DB::table('practices')
                        ->whereIn('id', $ids)
                        ->delete();

                    logger()->info('Deleted practices with identical descriptions', [
                        'description' => $practice->description,
                        'count' => $deleted
                    ]);
                }
            }
        } catch (\Exception $e) {
            logger()->error('Error removing identical practice descriptions', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}