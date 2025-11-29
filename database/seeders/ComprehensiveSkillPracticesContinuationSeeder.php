<?php

namespace Database\Seeders;

use App\Models\Practice;
use App\Models\Skill;
use App\Models\SkillArea;
use Illuminate\Database\Seeder;

class ComprehensiveSkillPracticesContinuationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // SkillArea mapping
        $skillAreas = [
            'Collaboration' => SkillArea::firstOrCreate(['name' => 'Collaboration']),
            'Communication' => SkillArea::firstOrCreate(['name' => 'Communication']),
            'Critical Thinking' => SkillArea::firstOrCreate(['name' => 'Critical Thinking']),
            'Project Management' => SkillArea::firstOrCreate(['name' => 'Project Management']),
        ];

        // More practices data - focusing on the remaining areas
        $additionalPractices = [
            // Conflict Management
            ['skill' => 'Conflict Management', 'description' => 'Listening patiently to divergent ideas that arose'],
            ['skill' => 'Conflict Management', 'description' => 'Acknowledging that personal preference may not be the best answer'],
            ['skill' => 'Conflict Management', 'description' => 'Seeking to understand the underlying details of the disagreement'],
            ['skill' => 'Conflict Management', 'description' => 'Seeking to understand someone else\'s perspective'],
            ['skill' => 'Conflict Management', 'description' => 'Accepting a less biased perspective when personally involved in heated disagreement'],
            ['skill' => 'Conflict Management', 'description' => 'Focusing on the broader goal in order to resolve conflict'],
            ['skill' => 'Conflict Management', 'description' => 'Acknowledging conflicting ideas in a constructive manner'],
            ['skill' => 'Conflict Management', 'description' => 'Pulling aside individuals with a disagreement after it became bigger'],
            ['skill' => 'Conflict Management', 'description' => 'Pulling aside individuals with a disagreement before it becomes bigger'],
            ['skill' => 'Conflict Management', 'description' => 'Maintaining the project vision as a guide to resolution'],
            ['skill' => 'Conflict Management', 'description' => 'Balancing personal needs of individuals with project work'],
            ['skill' => 'Conflict Management', 'description' => 'Maintaining neutral stance when disagreement and conflict is based on personal differences of opinion'],
            ['skill' => 'Conflict Management', 'description' => 'Calling in someone who wants to act unethically'],
            ['skill' => 'Conflict Management', 'description' => 'Upholding ethical action and vision amidst conflict'],
            ['skill' => 'Conflict Management', 'description' => 'Avoiding acting defensively'],
            ['skill' => 'Conflict Management', 'description' => 'Allowing space for disagreement through constructive discussion'],
            ['skill' => 'Conflict Management', 'description' => 'Identifying potential win-win options'],
            ['skill' => 'Conflict Management', 'description' => 'Promoting discussion of win-win option(s)'],

            // Cross-Cultural Understanding & Empathy
            ['skill' => 'Cross-Cultural Understanding & Empathy', 'description' => 'Seeking information on cross-cultural differences'],
            ['skill' => 'Cross-Cultural Understanding & Empathy', 'description' => 'Acknowledging others may have different beliefs and values that inform their decisions and actions'],
            ['skill' => 'Cross-Cultural Understanding & Empathy', 'description' => 'Practicing a cultural norm different than your own'],
            ['skill' => 'Cross-Cultural Understanding & Empathy', 'description' => 'Recognizing that although there may be cultural differences, does not make one way right or better'],

            // Trust-Building
            ['skill' => 'Trust-Building', 'description' => 'Reserving judgement about another\'s opinion or idea'],
            ['skill' => 'Trust-Building', 'description' => 'Following through on assignment or request asked of me/you'],
            ['skill' => 'Trust-Building', 'description' => 'Following up with someone who said they would complete something'],
            ['skill' => 'Trust-Building', 'description' => 'Following up on uncompleted task with trying to understand why the delay'],
            ['skill' => 'Trust-Building', 'description' => 'Following up on uncompleted task by understanding the delay and aiming to arrive at way forward'],
            ['skill' => 'Trust-Building', 'description' => 'Maintaining confidentiality of another\'s comments'],
            ['skill' => 'Trust-Building', 'description' => 'Making another comfortable with sharing personal information'],
            ['skill' => 'Trust-Building', 'description' => 'Acknowledging personal mistake'],
            ['skill' => 'Trust-Building', 'description' => 'Apologizing after making mistake or unintentionally insulting another'],
            ['skill' => 'Trust-Building', 'description' => 'Ensuring what you said actually happens'],
            ['skill' => 'Trust-Building', 'description' => 'Encouraging another to ensure what they said actually happens'],

            // Positivity Towards Team
            ['skill' => 'Positivity Towards Team', 'description' => 'Checking in with teammates outside of structured meeting time'],
            ['skill' => 'Positivity Towards Team', 'description' => 'Showing concern for issues others are facing'],
            ['skill' => 'Positivity Towards Team', 'description' => 'Smiling or otherwise showing positive attitude about team\'s efforts'],
            ['skill' => 'Positivity Towards Team', 'description' => 'Recognizing effort or contribution of a teammate'],
            ['skill' => 'Positivity Towards Team', 'description' => 'Celebrating an achievement of the team'],
            ['skill' => 'Positivity Towards Team', 'description' => 'Encouraging teammate who appears to be struggling'],
            ['skill' => 'Positivity Towards Team', 'description' => 'Reminding the team of the big picture impact'],
            ['skill' => 'Positivity Towards Team', 'description' => 'Expressing confidence in team or teammate'],
            ['skill' => 'Positivity Towards Team', 'description' => 'Encouraging cooperation among teammates handling complex tasks'],

            // Personal Flexibility
            ['skill' => 'Personal Flexibility', 'description' => 'Readiness to take on new task'],
            ['skill' => 'Personal Flexibility', 'description' => 'Asking how I/you can help when plans need to change'],
            ['skill' => 'Personal Flexibility', 'description' => 'Communicating according to style and channel best for team'],
            ['skill' => 'Personal Flexibility', 'description' => 'Attempting task with limited prior experience'],
            ['skill' => 'Personal Flexibility', 'description' => 'Seeing how my/your specific task fits into bigger team effort'],
            ['skill' => 'Personal Flexibility', 'description' => 'Assisting teammate with new task they have limited experience with'],
            ['skill' => 'Personal Flexibility', 'description' => 'Making the most of an unexpected situation'],
            ['skill' => 'Personal Flexibility', 'description' => 'Balancing personal needs with team needs'],

            // Communication - Sharing Ideas & Instructions Clearly
            ['skill' => 'Sharing Ideas & Instructions Clearly', 'description' => 'Explaining idea in a structured manner, identifying the 3 or 4 main points'],
            ['skill' => 'Sharing Ideas & Instructions Clearly', 'description' => 'Organizing explanation into sequential process'],
            ['skill' => 'Sharing Ideas & Instructions Clearly', 'description' => 'Checking in with others about their current understanding before explaining more'],
            ['skill' => 'Sharing Ideas & Instructions Clearly', 'description' => 'Asking others to share back idea to confirm understanding and next step'],
            ['skill' => 'Sharing Ideas & Instructions Clearly', 'description' => 'Articulating the key take-away first before adding details'],
            ['skill' => 'Sharing Ideas & Instructions Clearly', 'description' => 'Making explicit the one next step expected of others'],
            ['skill' => 'Sharing Ideas & Instructions Clearly', 'description' => 'Making clear logical connections in idea (ie. if X, then Y)'],
            ['skill' => 'Sharing Ideas & Instructions Clearly', 'description' => 'Speaking at volume which others can hear'],
            ['skill' => 'Sharing Ideas & Instructions Clearly', 'description' => 'Gathering thoughts before speaking'],
            ['skill' => 'Sharing Ideas & Instructions Clearly', 'description' => 'Speaking up at a reasonable point in a conversation'],
            ['skill' => 'Sharing Ideas & Instructions Clearly', 'description' => 'Speaking at speed others can understand clearly'],
            ['skill' => 'Sharing Ideas & Instructions Clearly', 'description' => 'Using an anecdote to communicate a point'],
            ['skill' => 'Sharing Ideas & Instructions Clearly', 'description' => 'Explaining why specific approach is used']
        ];

        $this->processSkillsAndPractices($skillAreas, $additionalPractices);
    }

    private function processSkillsAndPractices($skillAreas, $practices)
    {
        foreach ($practices as $i => $practiceInfo) {
            // Get the skill
            $skill = Skill::where('name', $practiceInfo['skill'])->first();

            if (!$skill) {
                // If skill doesn't exist, determine its area
                $areaName = null;
                foreach (['Collaboration', 'Communication', 'Critical Thinking', 'Project Management'] as $area) {
                    if (strpos($practiceInfo['skill'], $area) !== false) {
                        $areaName = $area;
                        break;
                    }
                }

                if (!$areaName) {
                    // Default to Collaboration if can't determine
                    $areaName = 'Collaboration';
                }

                // Create the skill
                $skill = Skill::create([
                    'name' => $practiceInfo['skill'],
                    'skill_area_id' => $skillAreas[$areaName]->id,
                    'description' => $practiceInfo['skill']
                ]);
            }

            // Only create the practice if it does not already exist for this skill
            if (!Practice::where('skill_id', $skill->id)->where('description', $practiceInfo['description'])->exists()) {
                Practice::create([
                    'skill_id' => $skill->id,
                    'name' => $this->generatePracticeName($practiceInfo['description']),
                    'description' => $practiceInfo['description'],
                    'order' => $i+1,
                ]);
            }
        }
    }

    private function generatePracticeName($description)
    {
        // Generate a short name from the description
        // Limit to first 30 characters and add ellipsis if longer
        return strlen($description) > 30 ?
            substr($description, 0, 30) . '...' :
            $description;
    }
}
