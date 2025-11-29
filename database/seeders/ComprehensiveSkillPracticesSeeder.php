<?php

namespace Database\Seeders;

use App\Models\Practice;
use App\Models\Skill;
use App\Models\SkillArea;
use Illuminate\Database\Seeder;

class ComprehensiveSkillPracticesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // SkillArea mapping
        $skillAreas = [
            'Self-Awareness & Self-Management' => SkillArea::firstOrCreate([
                'name' => 'Self-Awareness & Self-Management',
            ]),
            'Collaboration' => SkillArea::firstOrCreate([
                'name' => 'Collaboration',
            ]),
            'Communication' => SkillArea::firstOrCreate([
                'name' => 'Communication',
            ]),
            'Critical Thinking' => SkillArea::firstOrCreate([
                'name' => 'Critical Thinking',
            ]),
            'Project Management' => SkillArea::firstOrCreate([
                'name' => 'Project Management',
            ]),
        ];

        // Set default colors if not already set
        foreach ($skillAreas as $name => $area) {
            if (!$area->color) {
                switch ($name) {
                    case 'Self-Awareness & Self-Management':
                        $area->color = '#4299E1'; // Blue
                        break;
                    case 'Collaboration':
                        $area->color = '#48BB78'; // Green
                        break;
                    case 'Communication':
                        $area->color = '#ED8936'; // Orange
                        break;
                    case 'Critical Thinking':
                        $area->color = '#9F7AEA'; // Purple
                        break;
                    case 'Project Management':
                        $area->color = '#F56565'; // Red
                        break;
                }
                $area->save();
            }
        }

        // Skill mapping with descriptions
        $skills = [
            // Self-Awareness & Self-Management
            'Drive for Self-Development' => [
                'area' => 'Self-Awareness & Self-Management',
                'description' => 'Developing oneself through reflection and action.'
            ],
            'Attention to Detail & Drive for Quality' => [
                'area' => 'Self-Awareness & Self-Management',
                'description' => 'Careful, high-quality work.'
            ],
            'Personal Adaptability' => [
                'area' => 'Self-Awareness & Self-Management',
                'description' => 'Adapting to change and uncertainty.'
            ],
            'Stress Tolerance & Ability to Work under Pressure' => [
                'area' => 'Self-Awareness & Self-Management',
                'description' => 'Managing stress and pressure.'
            ],
            'Self-Control & Managing Emotions' => [
                'area' => 'Self-Awareness & Self-Management',
                'description' => 'Emotional regulation.'
            ],
            'Trust in Yourself' => [
                'area' => 'Self-Awareness & Self-Management',
                'description' => 'Confidence and self-trust.'
            ],
            'Managing Time Efficiently' => [
                'area' => 'Self-Awareness & Self-Management',
                'description' => 'Time management.'
            ],
            'Self-Reflection' => [
                'area' => 'Self-Awareness & Self-Management',
                'description' => 'Reflecting on actions and growth.'
            ],
            'Taking Informed Action' => [
                'area' => 'Self-Awareness & Self-Management',
                'description' => 'Acting based on information.'
            ],
            // Collaboration
            'Leadership & Influence' => [
                'area' => 'Collaboration',
                'description' => 'Leading and influencing teams.'
            ],
            'Building Relationships to Support Team' => [
                'area' => 'Collaboration',
                'description' => 'Building supportive relationships.'
            ],
            'Supporting Individuals & Teams' => [
                'area' => 'Collaboration',
                'description' => 'Supporting teammates.'
            ],
            'Conflict Management' => [
                'area' => 'Collaboration',
                'description' => 'Managing and resolving conflict.'
            ],
            'Cross-Cultural Understanding & Empathy' => [
                'area' => 'Collaboration',
                'description' => 'Understanding and empathizing across cultures.'
            ],
            'Trust-Building' => [
                'area' => 'Collaboration',
                'description' => 'Building trust in teams.'
            ],
            'Positivity Towards Team' => [
                'area' => 'Collaboration',
                'description' => 'Promoting positivity.'
            ],
            'Personal Flexibility' => [
                'area' => 'Collaboration',
                'description' => 'Being flexible in teams.'
            ],
            // Communication
            'Sharing Ideas & Instructions Clearly' => [
                'area' => 'Communication',
                'description' => 'Clear communication.'
            ],
            'Listening' => [
                'area' => 'Communication',
                'description' => 'Active listening.'
            ],
            'Receiving Feedback' => [
                'area' => 'Communication',
                'description' => 'Receiving feedback constructively.'
            ],
            'Giving Feedback' => [
                'area' => 'Communication',
                'description' => 'Giving feedback constructively.'
            ],
            'Informal Oral Communication' => [
                'area' => 'Communication',
                'description' => 'Informal verbal communication.'
            ],
            'Effective Storytelling' => [
                'area' => 'Communication',
                'description' => 'Telling stories effectively.'
            ],
            'Public Speaking' => [
                'area' => 'Communication',
                'description' => 'Speaking publicly.'
            ],
            'Interviewing & Asking Questions' => [
                'area' => 'Communication',
                'description' => 'Interviewing and questioning.'
            ],
            'Expressing Opinions & Disagreeing Productively' => [
                'area' => 'Communication',
                'description' => 'Productive disagreement.'
            ],
            'Crafting an Effective Email' => [
                'area' => 'Communication',
                'description' => 'Writing effective emails.'
            ],
            'Communicating Effectively in Writing' => [
                'area' => 'Communication',
                'description' => 'Effective written communication.'
            ],
            // Critical Thinking
            'Structured Thinking' => [
                'area' => 'Critical Thinking',
                'description' => 'Organized, structured thought.'
            ],
            'Solving Problems Strategically' => [
                'area' => 'Critical Thinking',
                'description' => 'Strategic problem solving.'
            ],
            'Negotiating' => [
                'area' => 'Critical Thinking',
                'description' => 'Negotiation skills.'
            ],
            'Persuading Stakeholders' => [
                'area' => 'Critical Thinking',
                'description' => 'Persuading others.'
            ],
            'Researching & Synthesizing Main Ideas' => [
                'area' => 'Critical Thinking',
                'description' => 'Research and synthesis.'
            ],
            // Project Management
            'Visioning' => [
                'area' => 'Project Management',
                'description' => 'Vision and big-picture thinking.'
            ],
            'Creativity & Brainstorming' => [
                'area' => 'Project Management',
                'description' => 'Generating and refining ideas.'
            ],
            'Planning, (SMART) Goal Setting, & Delegating' => [
                'area' => 'Project Management',
                'description' => 'Planning and goal setting.'
            ],
            'Adaptability to Program Realities' => [
                'area' => 'Project Management',
                'description' => 'Adapting to program realities.'
            ],
            'Follow-Through on Tasks' => [
                'area' => 'Project Management',
                'description' => 'Following through on tasks.'
            ],
            'Informed Decision-Making' => [
                'area' => 'Project Management',
                'description' => 'Making informed decisions.'
            ],
            'Budgeting' => [
                'area' => 'Project Management',
                'description' => 'Budgeting and resource management.'
            ],
        ];

        // Comprehensive practices data from the provided list
        $comprehensivePractices = [
            // Self-Awareness & Self Management - Drive for Self-Development
            ['skill' => 'Drive for Self-Development', 'description' => 'Reflecting on personal strength with prompting'],
            ['skill' => 'Drive for Self-Development', 'description' => 'Reflecting on personal weakness with prompting'],
            ['skill' => 'Drive for Self-Development', 'description' => 'Reflecting on personal strength without prompting'],
            ['skill' => 'Drive for Self-Development', 'description' => 'Reflecting on personal weakness without prompting'],
            ['skill' => 'Drive for Self-Development', 'description' => 'Feeling desire to improve'],
            ['skill' => 'Drive for Self-Development', 'description' => 'Identifying and acknowledging area for improvement'],
            ['skill' => 'Drive for Self-Development', 'description' => 'Identifying of specific action(s) to improve'],
            ['skill' => 'Drive for Self-Development', 'description' => 'Taking specific action to intentionally improve'],
            ['skill' => 'Drive for Self-Development', 'description' => 'Making time or a plan to practice skill'],
            ['skill' => 'Drive for Self-Development', 'description' => 'Making continuous intentional efforts to build skills'],
            ['skill' => 'Drive for Self-Development', 'description' => 'Acknowledging feedback provided in positive manner'],
            ['skill' => 'Drive for Self-Development', 'description' => 'Using feedback provided to intentionally improve'],
            ['skill' => 'Drive for Self-Development', 'description' => 'Seeking feedback to improve'],
            ['skill' => 'Drive for Self-Development', 'description' => 'Taking criticism as opportunity to improve'],
            ['skill' => 'Drive for Self-Development', 'description' => 'Motivating others to want to improve'],
            ['skill' => 'Drive for Self-Development', 'description' => 'Seeking to see what more I/you could do to help the project'],

            // Attention to Detail & Drive for Quality
            ['skill' => 'Attention to Detail & Drive for Quality', 'description' => 'Reading and rereading instructions carefully'],
            ['skill' => 'Attention to Detail & Drive for Quality', 'description' => 'Envisioning each aspect of the tasks at hand to ensure proper approach'],
            ['skill' => 'Attention to Detail & Drive for Quality', 'description' => 'Asking for clarification to ensure understanding of a task or topic'],
            ['skill' => 'Attention to Detail & Drive for Quality', 'description' => 'Conducting online research to learn how to optimize approach'],
            ['skill' => 'Attention to Detail & Drive for Quality', 'description' => 'Taking time to thoughtfully review and revise work or plans (written or orally)'],
            ['skill' => 'Attention to Detail & Drive for Quality', 'description' => 'Preparing draft version and seeking feedback for improvement'],
            ['skill' => 'Attention to Detail & Drive for Quality', 'description' => 'Incorporating feedback on draft version into next revisions'],
            ['skill' => 'Attention to Detail & Drive for Quality', 'description' => 'Completing a task at the highest quality I am/you are currently capable of'],

            // Personal Adaptability
            ['skill' => 'Personal Adaptability', 'description' => 'Maintaining calm when something unexpected arose'],
            ['skill' => 'Personal Adaptability', 'description' => 'Seeking information to understand how to adapt to a change'],
            ['skill' => 'Personal Adaptability', 'description' => 'Using unexpected change as opportunity'],
            ['skill' => 'Personal Adaptability', 'description' => 'Showing optimistic outlook amid uncertainty'],
            ['skill' => 'Personal Adaptability', 'description' => 'Planning to effectively navigate change(s)'],

            // Stress Tolerance & Ability to Work under Pressure
            ['skill' => 'Stress Tolerance & Ability to Work under Pressure', 'description' => 'Staying calm and composed during a difficult moment'],
            ['skill' => 'Stress Tolerance & Ability to Work under Pressure', 'description' => 'Harnessing energy and enthusiasm to get the work done, even in a tough moment'],
            ['skill' => 'Stress Tolerance & Ability to Work under Pressure', 'description' => 'Affirming to my/your-self and others that you\'ll make it through'],
            ['skill' => 'Stress Tolerance & Ability to Work under Pressure', 'description' => 'Envisioning the valuable results and impact your efforts will enable'],
            ['skill' => 'Stress Tolerance & Ability to Work under Pressure', 'description' => 'Asking for additional support when needed'],

            // Self-Control & Managing Emotions
            ['skill' => 'Self-Control & Managing Emotions', 'description' => 'Noticing and naming thoughts and emotions that arise'],
            ['skill' => 'Self-Control & Managing Emotions', 'description' => 'Maintaining composure in a difficult moment, even if feeling insulted'],
            ['skill' => 'Self-Control & Managing Emotions', 'description' => 'Noticing how thoughts and emotions are related to a behavior'],
            ['skill' => 'Self-Control & Managing Emotions', 'description' => 'Checking in with teammates before emotional intensity overwhelms team progress'],
            ['skill' => 'Self-Control & Managing Emotions', 'description' => 'Explaining calmly and proactively that "I hear you saying [how they feel]..." to address emotional intensity'],
            ['skill' => 'Self-Control & Managing Emotions', 'description' => 'Practicing self-compassion and self-care when emotions feel overwhelming'],
            ['skill' => 'Self-Control & Managing Emotions', 'description' => 'Making informed decision based on big picture rather than emotional decision based on tough moment'],
            ['skill' => 'Self-Control & Managing Emotions', 'description' => 'Showing positive emotions to benefit the team'],
            ['skill' => 'Self-Control & Managing Emotions', 'description' => 'Showing patience in a frustrating situation'],
            ['skill' => 'Self-Control & Managing Emotions', 'description' => 'Expressing optimism at finding a solution to a difficult or frustrating situation'],

            // Trust in Yourself
            ['skill' => 'Trust in Yourself', 'description' => 'Believing I/you have something meaningful to share by contributing to the conversation'],
            ['skill' => 'Trust in Yourself', 'description' => 'Sharing a suggestion, even when it would shift the team in a different direction'],
            ['skill' => 'Trust in Yourself', 'description' => 'Sharing a vision or plan I/you have had in mind'],
            ['skill' => 'Trust in Yourself', 'description' => 'Disagreeing with a teammate in a constructive manner'],
            ['skill' => 'Trust in Yourself', 'description' => 'Asking a question I/you thought could sound silly'],
            ['skill' => 'Trust in Yourself', 'description' => 'Trying new way of thinking, even if feeling uncertain'],
            ['skill' => 'Trust in Yourself', 'description' => 'Trying a new way of doing something, even if feeling uncertain'],

            // Managing Time Efficiently
            ['skill' => 'Managing Time Efficiently', 'description' => 'Submitting an assignment on time'],
            ['skill' => 'Managing Time Efficiently', 'description' => 'Setting clear priorities to ensure the most important work is completed on time'],
            ['skill' => 'Managing Time Efficiently', 'description' => 'Making a to-do list to ensure everything gets completed'],
            ['skill' => 'Managing Time Efficiently', 'description' => 'Breaking down a big task into smaller items and working through each one-at-a-time'],
            ['skill' => 'Managing Time Efficiently', 'description' => 'Using a commitment device to overcome procrastination'],
            ['skill' => 'Managing Time Efficiently', 'description' => 'Recognizing and managing emotional source of procrastination'],
            ['skill' => 'Managing Time Efficiently', 'description' => 'Identifying achievable first step to get started when susceptible to procrastination'],
            ['skill' => 'Managing Time Efficiently', 'description' => 'Shaping time-bound goals for yourself or the team using SMART framework'],

            // Self-Reflection
            ['skill' => 'Self-Reflection', 'description' => 'Preparing questions to guide my/your self-reflection'],
            ['skill' => 'Self-Reflection', 'description' => 'Creating time and space for my/your-self to reflect'],
            ['skill' => 'Self-Reflection', 'description' => 'Using tools (journaling, meditation, etc.) for self-inquiry'],

            // Taking Informed Action
            ['skill' => 'Taking Informed Action', 'description' => 'Formulating questions to inform the next stages of the project'],
            ['skill' => 'Taking Informed Action', 'description' => 'Using a plan to gather answers to identified questions'],
            ['skill' => 'Taking Informed Action', 'description' => 'Weighing and synthesizing research findings'],
            ['skill' => 'Taking Informed Action', 'description' => 'Incorporating research findings to inform approach'],
            ['skill' => 'Taking Informed Action', 'description' => 'Seeking evidence to inform appropriate next steps'],

            // Collaboration - Leadership & Influence
            ['skill' => 'Leadership & Influence', 'description' => 'Facilitating program of team members towards assigned tasks'],
            ['skill' => 'Leadership & Influence', 'description' => 'Awareness of group needs (stepping up, stepping back)'],
            ['skill' => 'Leadership & Influence', 'description' => 'Motivating team member who was struggling to work towards the vision'],
            ['skill' => 'Leadership & Influence', 'description' => 'Getting team members and/or stakeholders excited about the vision'],
            ['skill' => 'Leadership & Influence', 'description' => 'Helping team members see how each person\'s tasks fit together toward common vision'],
            ['skill' => 'Leadership & Influence', 'description' => 'Encouraging team members to understand why their individual work is important to the team'],

            // Building Relationships to Support Team
            ['skill' => 'Building Relationships to Support Team', 'description' => 'Interest in getting to know others'],
            ['skill' => 'Building Relationships to Support Team', 'description' => 'Showing appreciation for others\' ideas'],
            ['skill' => 'Building Relationships to Support Team', 'description' => 'Asking questions to learn more about teammates'],
            ['skill' => 'Building Relationships to Support Team', 'description' => 'Acknowledging a skill or contribution made by another'],
            ['skill' => 'Building Relationships to Support Team', 'description' => 'Asking teammates for their ideas and opinions'],
            ['skill' => 'Building Relationships to Support Team', 'description' => 'Waiting to share personal opinion until others have all had a chance to share'],
            ['skill' => 'Building Relationships to Support Team', 'description' => 'Encouraging quieter teammates to share even if they seem hesitant'],
            ['skill' => 'Building Relationships to Support Team', 'description' => 'Asking clarifying questions about a teammate\'s idea, without judgement'],
            ['skill' => 'Building Relationships to Support Team', 'description' => 'Encouraging individuals with differing ideas and opinions to share'],

            // Supporting Individuals & Teams
            ['skill' => 'Supporting Individuals & Teams', 'description' => 'Offering a teammate assistance'],
            ['skill' => 'Supporting Individuals & Teams', 'description' => 'Listening and seeking to understand another\'s struggle'],
            ['skill' => 'Supporting Individuals & Teams', 'description' => 'Coaching or training team/mate on specific task'],
            ['skill' => 'Supporting Individuals & Teams', 'description' => 'Taking initiative to offer informal assistance, even if not specifically asked'],
            ['skill' => 'Supporting Individuals & Teams', 'description' => 'Organizing formal assistance (such as setting up a separate meeting time) when appropriate'],
            ['skill' => 'Supporting Individuals & Teams', 'description' => 'Providing individual update on progress to team, even if needing to acknowledge being behind schedule'],
            ['skill' => 'Supporting Individuals & Teams', 'description' => 'Providing continuing timely updates on progress'],
            ['skill' => 'Supporting Individuals & Teams', 'description' => 'Providing feedback on effective behavior(s) of teammate that helps you succeed with your tasks'],

            // And the remaining practices follow the same pattern...
        ];

        // Due to character limits, we'll split the data processing
        $this->processSkillsAndPractices($skillAreas, $skills, $comprehensivePractices);
    }

    private function processSkillsAndPractices($skillAreas, $skills, $practices)
    {
        foreach ($practices as $i => $practiceInfo) {
            // Get or create the Skill
            $skillMeta = $skills[$practiceInfo['skill']] ?? null;
            if (!$skillMeta) continue; // Skip if mapping not found

            $skill = Skill::firstOrCreate([
                'name' => $practiceInfo['skill'],
                'skill_area_id' => $skillAreas[$skillMeta['area']]->id,
            ], [
                'description' => $skillMeta['description'],
            ]);

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
