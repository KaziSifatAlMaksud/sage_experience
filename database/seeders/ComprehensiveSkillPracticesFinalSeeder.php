<?php

namespace Database\Seeders;

use App\Models\Practice;
use App\Models\Skill;
use App\Models\SkillArea;
use Illuminate\Database\Seeder;

class ComprehensiveSkillPracticesFinalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // SkillArea mapping
        $skillAreas = [
            'Communication' => SkillArea::firstOrCreate(['name' => 'Communication']),
            'Critical Thinking' => SkillArea::firstOrCreate(['name' => 'Critical Thinking']),
            'Project Management' => SkillArea::firstOrCreate(['name' => 'Project Management']),
        ];

        // Final set of practices data
        $finalPractices = [
            // Listening
            ['skill' => 'Listening', 'description' => 'Using body language (such as eye contact) appropriately to show interest in what the speaker is communicating'],
            ['skill' => 'Listening', 'description' => 'Nodding or acknowledging what someone is sharing in a manner that does not interrupt (smiling, shaking head, relevant facial expressions, etc.)'],
            ['skill' => 'Listening', 'description' => 'Asking clarifying questions at appropriate moments'],
            ['skill' => 'Listening', 'description' => 'Asking clarifying questions in manner that shows you value the speaker\'s ideas'],
            ['skill' => 'Listening', 'description' => 'Avoiding sharing own ideas until speaker has finished'],
            ['skill' => 'Listening', 'description' => 'Summarizing what speaker said to confirm understanding'],
            ['skill' => 'Listening', 'description' => 'Waiting out pauses and silences'],
            ['skill' => 'Listening', 'description' => 'Not interrupting someone while speaking'],
            ['skill' => 'Listening', 'description' => 'Showing patience to truly understand another\'s idea or opinion'],

            // Remaining Communication skills
            ['skill' => 'Receiving Feedback', 'description' => 'Being intentional with body language (notably eye contact) with person sharing feedback (if done in person)'],
            ['skill' => 'Receiving Feedback', 'description' => 'Viewing feedback as way to improve'],
            ['skill' => 'Receiving Feedback', 'description' => 'Responding to the person sharing the feedback in an appropriate manner (such as showing gratitude for their input)'],
            ['skill' => 'Receiving Feedback', 'description' => 'Embracing feedback without getting defensive'],
            ['skill' => 'Receiving Feedback', 'description' => 'Acknowledging that teammate\'s perspective is valuable, even if I/you saw the situation differently'],
            ['skill' => 'Receiving Feedback', 'description' => 'Identifying specific next steps you can take to improve based on feedback'],

            ['skill' => 'Giving Feedback', 'description' => 'Being intentional with body language (notably eye contact) with person receiving feedback (if done in person)'],
            ['skill' => 'Giving Feedback', 'description' => 'Offering specific examples to help receiver understand precise actions that can be improved'],
            ['skill' => 'Giving Feedback', 'description' => 'Preparing in advance to share most important feedback'],
            ['skill' => 'Giving Feedback', 'description' => 'Focusing on just 1 or 2 specific items for improvement at a time'],
            ['skill' => 'Giving Feedback', 'description' => 'Offering balance of positive feedback and constructive feedback for improvement'],
            ['skill' => 'Giving Feedback', 'description' => 'Helping receiver see how specific changes in behavior can help team achieve big impact'],
            ['skill' => 'Giving Feedback', 'description' => 'Giving receiver opportunity to practice behavior for improvement, reminding them of opportunity when it arises'],

            // Critical Thinking
            ['skill' => 'Structured Thinking', 'description' => 'Listing the relevant aspects or points that are part of your idea'],
            ['skill' => 'Structured Thinking', 'description' => 'Prioritizing the most important aspects or points'],
            ['skill' => 'Structured Thinking', 'description' => 'Not getting caught up in low-priority aspects or points'],
            ['skill' => 'Structured Thinking', 'description' => 'Presenting my idea in a visual format such as a table or three key take-aways'],
            ['skill' => 'Structured Thinking', 'description' => 'Identifying a basic formula that can be used to express the relationship of the various aspects of your idea'],
            ['skill' => 'Structured Thinking', 'description' => 'Identifying helpful quantitative or qualitative inputs to plug into a formula to approach a desired insight'],
            ['skill' => 'Structured Thinking', 'description' => 'Using a spreadsheet or table to capture complex details'],
            ['skill' => 'Structured Thinking', 'description' => 'Building a spreadsheet that captures only the necessary aspects using effective naming or rows and columns'],
            ['skill' => 'Structured Thinking', 'description' => 'Using quantitative or qualitative inputs in a spreadsheet to enable manipulation'],
            ['skill' => 'Structured Thinking', 'description' => 'Applying consistent formatting of inputs in a spreadsheet to enable easy manipulation'],
            ['skill' => 'Structured Thinking', 'description' => 'Incorporating simple but effective formatting in a spreadsheet such as colored headers or bolded totals to enable easy reading'],

            ['skill' => 'Solving Problems Strategically', 'description' => 'Seeking new information to inform understanding of topic'],
            ['skill' => 'Solving Problems Strategically', 'description' => 'Identifying potential drivers (causes) of the problem'],
            ['skill' => 'Solving Problems Strategically', 'description' => 'Focusing in on the most relevant drivers of the problem'],
            ['skill' => 'Solving Problems Strategically', 'description' => 'Using information collected to understand actions that make a driver stronger or weaker'],
            ['skill' => 'Solving Problems Strategically', 'description' => 'Using logic (cause & effect thinking) to understand and explain problem'],
            ['skill' => 'Solving Problems Strategically', 'description' => 'Recognizing there are solutions to big problems, even if you/I don\'t see them right away'],
            ['skill' => 'Solving Problems Strategically', 'description' => 'Working to identify a root cause of a problem'],

            // Project Management skills
            ['skill' => 'Visioning', 'description' => 'Imagining new ways of approaching the identified problem'],
            ['skill' => 'Visioning', 'description' => 'Seeing the specific actions required of various stakeholders to achieve the new way of approaching the problem'],
            ['skill' => 'Visioning', 'description' => 'Articulating a big idea for approaching the problem that connects with others on an emotional level'],
            ['skill' => 'Visioning', 'description' => 'Articulating a big idea for approaching the problem that connects with others on an intellectual and logical level'],
            ['skill' => 'Visioning', 'description' => 'Sharing the vision in a succinct manner (just 1-3 sentences written, or 30-60 seconds orally)'],
            ['skill' => 'Visioning', 'description' => 'Getting stakeholders excited about the vision'],

            ['skill' => 'Creativity & Brainstorming', 'description' => 'Generating many ideas'],
            ['skill' => 'Creativity & Brainstorming', 'description' => 'Offering constructive feedback to drive the generation of additional ideas'],
            ['skill' => 'Creativity & Brainstorming', 'description' => 'Incorporating feedback from others to identify new and improved ideas'],
            ['skill' => 'Creativity & Brainstorming', 'description' => 'Avoiding negative interactions that may inhibit others from sharing new ideas'],
            ['skill' => 'Creativity & Brainstorming', 'description' => 'Creating a positive space to promote the generation of divergent ideas'],
            ['skill' => 'Creativity & Brainstorming', 'description' => 'Providing a process for the team to generate lots of ideas'],
            ['skill' => 'Creativity & Brainstorming', 'description' => 'Providing a process for the team to generate deeper and tailored ideas'],
            ['skill' => 'Creativity & Brainstorming', 'description' => 'Leveraging outside resources to bring in new ideas'],
            ['skill' => 'Creativity & Brainstorming', 'description' => 'Enabling a mental space that allows people to think divergently'],

            ['skill' => 'Budgeting', 'description' => 'Identifying resources required for a program, including invisible items like transport and communication costs'],
            ['skill' => 'Budgeting', 'description' => 'Finding appropriate prices of resources required'],
            ['skill' => 'Budgeting', 'description' => 'Identifying alternative options when costs are too high'],
            ['skill' => 'Budgeting', 'description' => 'Structuring budget in a spreadsheet that identifying source of item, cost, quantity required, and total costs']
        ];

        $this->processSkillsAndPractices($skillAreas, $finalPractices);
    }

    private function processSkillsAndPractices($skillAreas, $practices)
    {
        foreach ($practices as $i => $practiceInfo) {
            // Get the skill
            $skill = Skill::where('name', $practiceInfo['skill'])->first();

            if (!$skill) {
                // If skill doesn't exist, determine its area
                $areaName = null;
                foreach (['Communication', 'Critical Thinking', 'Project Management'] as $area) {
                    if (strpos($practiceInfo['skill'], $area) !== false) {
                        $areaName = $area;
                        break;
                    }
                }

                if (!$areaName) {
                    // Default to Communication if can't determine
                    $areaName = 'Communication';
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
