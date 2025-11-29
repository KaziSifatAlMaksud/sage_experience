<?php

namespace Database\Seeders;

use App\Models\Practice;
use App\Models\Skill;
use App\Models\SkillArea;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SkillsAndPracticesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // === BEGIN: AUTO-SEEDED PRACTICES FROM EXCEL LIST ===
        // SkillArea mapping from the SkillAreasSeeder
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

        // Skill mapping - you can expand/modify as needed
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

        // --- PRACTICES FROM THE EXCEL LIST ---
        $practiceSeedData = [
            // Self-Awareness & Self Management - Drive for Self-Development
            ['skill' => 'Drive for Self-Development', 'name' => 'Reflecting on personal strength with prompting'],
            ['skill' => 'Drive for Self-Development', 'name' => 'Reflecting on personal weakness with prompting'],
            ['skill' => 'Drive for Self-Development', 'name' => 'Reflecting on personal strength without prompting'],
            ['skill' => 'Drive for Self-Development', 'name' => 'Reflecting on personal weakness without prompting'],
            ['skill' => 'Drive for Self-Development', 'name' => 'Feeling desire to improve'],
            ['skill' => 'Drive for Self-Development', 'name' => 'Identifying and acknowledging area for improvement'],
            ['skill' => 'Drive for Self-Development', 'name' => 'Identifying of specific action(s) to improve'],
            ['skill' => 'Drive for Self-Development', 'name' => 'Taking specific action to intentionally improve'],
            ['skill' => 'Drive for Self-Development', 'name' => 'Making time or a plan to practice skill'],
            ['skill' => 'Drive for Self-Development', 'name' => 'Making continuous intentional efforts to build skills'],
            ['skill' => 'Drive for Self-Development', 'name' => 'Acknowledging feedback provided in positive manner'],
            ['skill' => 'Drive for Self-Development', 'name' => 'Using feedback provided to intentionally improve'],
            ['skill' => 'Drive for Self-Development', 'name' => 'Seeking feedback to improve'],
            ['skill' => 'Drive for Self-Development', 'name' => 'Taking criticism as opportunity to improve'],
            ['skill' => 'Drive for Self-Development', 'name' => 'Motivating others to want to improve'],
            ['skill' => 'Drive for Self-Development', 'name' => 'Seeking to see what more I/you could do to help the project'],
            // ... (repeat for each skill grouping from your list, organized by skill)
        ];

        foreach ($practiceSeedData as $i => $practiceInfo) {
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
            if (!Practice::where('skill_id', $skill->id)->where('name', $practiceInfo['name'])->exists()) {
                Practice::create([
                    'skill_id' => $skill->id,
                    'name' => $practiceInfo['name'],
                    'description' => $practiceInfo['name'], // Use name as description for now
                    'order' => $i+1,
                ]);
            }
        }
        // === END: AUTO-SEEDED PRACTICES FROM EXCEL LIST ===
        // Get the Collaboration skill area
        $collaborationArea = SkillArea::where('name', 'Collaboration')->first();

        // Skill 1: Offering a Teammate Assistance
        $skill1 = Skill::create([
            'name' => 'Offering a Teammate Assistance',
            'description' => 'Identify opportunities to help teammates and provide effective assistance.',
            'skill_area_id' => $collaborationArea->id,
        ]);

        Practice::create([
            'skill_id' => $skill1->id,
            'name' => 'Proactive Help',
            'description' => 'Identify when a teammate is struggling and offer help proactively.',
            'order' => 1,
        ]);

        Practice::create([
            'skill_id' => $skill1->id,
            'name' => 'Resource Sharing',
            'description' => 'Share resources or knowledge that could assist the teammate.',
            'order' => 2,
        ]);

        Practice::create([
            'skill_id' => $skill1->id,
            'name' => 'Follow-up',
            'description' => 'Follow up to ensure the teammate has successfully resolved the issue.',
            'order' => 3,
        ]);

        // Get the Critical Thinking skill area
        $criticalThinkingArea = SkillArea::where('name', 'Critical Thinking')->first();

        // Skill 2: Identifying Bias(es) from a Particular Source
        $skill2 = Skill::create([
            'name' => 'Identifying Bias(es) from a Particular Source',
            'description' => 'Recognize and address biases in information sources.',
            'skill_area_id' => $criticalThinkingArea->id,
        ]);

        Practice::create([
            'skill_id' => $skill2->id,
            'name' => 'Source Analysis',
            'description' => 'Analyze the source\'s background and potential motivations.',
            'order' => 1,
        ]);

        Practice::create([
            'skill_id' => $skill2->id,
            'name' => 'Comparison',
            'description' => 'Compare the source\'s information with other reliable sources.',
            'order' => 2,
        ]);

        Practice::create([
            'skill_id' => $skill2->id,
            'name' => 'Impact Reflection',
            'description' => 'Reflect on how the bias might affect the team\'s decision-making.',
            'order' => 3,
        ]);

        // Get the Project Management skill area
        $projectManagementArea = SkillArea::where('name', 'Project Management')->first();

        // Skill 3: Creating and Communicating a New Timeline When Needed
        $skill3 = Skill::create([
            'name' => 'Creating and Communicating a New Timeline When Needed',
            'description' => 'Effectively manage and communicate timeline changes.',
            'skill_area_id' => $projectManagementArea->id,
        ]);

        Practice::create([
            'skill_id' => $skill3->id,
            'name' => 'Impact Assessment',
            'description' => 'Assess the impact of the change on the project timeline.',
            'order' => 1,
        ]);

        Practice::create([
            'skill_id' => $skill3->id,
            'name' => 'Timeline Revision',
            'description' => 'Draft a revised timeline and share it with the team.',
            'order' => 2,
        ]);

        Practice::create([
            'skill_id' => $skill3->id,
            'name' => 'Change Communication',
            'description' => 'Communicate the reasons for the change and ensure everyone is aligned.',
            'order' => 3,
        ]);

        // Add more skills to fill out the skill areas

        // Communication skill area
        $communicationArea = SkillArea::where('name', 'Communication')->first();

        $skill4 = Skill::create([
            'name' => 'Building Relationships',
            'description' => 'Establish and maintain positive professional relationships.',
            'skill_area_id' => $communicationArea->id,
        ]);

        Practice::create([
            'skill_id' => $skill4->id,
            'name' => 'Conversation Initiation',
            'description' => 'Initiate conversations with new team members.',
            'order' => 1,
        ]);

        Practice::create([
            'skill_id' => $skill4->id,
            'name' => 'Active Interest',
            'description' => 'Show genuine interest in others\' perspectives and experiences.',
            'order' => 2,
        ]);

        Practice::create([
            'skill_id' => $skill4->id,
            'name' => 'Regular Communication',
            'description' => 'Maintain regular communication with team members.',
            'order' => 3,
        ]);

        // Self-Awareness skill area
        $selfAwarenessArea = SkillArea::where('name', 'Self-Awareness & Self-Management')->first();

        $skill5 = Skill::create([
            'name' => 'Reflecting on Performance',
            'description' => 'Analyze personal performance and identify areas for improvement.',
            'skill_area_id' => $selfAwarenessArea->id,
        ]);

        Practice::create([
            'skill_id' => $skill5->id,
            'name' => 'Performance Documentation',
            'description' => 'Document personal successes and challenges after key activities.',
            'order' => 1,
        ]);

        Practice::create([
            'skill_id' => $skill5->id,
            'name' => 'Feedback Collection',
            'description' => 'Ask for specific feedback from peers and mentors.',
            'order' => 2,
        ]);

        Practice::create([
            'skill_id' => $skill5->id,
            'name' => 'Goal Setting',
            'description' => 'Set concrete goals for improvement based on reflection.',
            'order' => 3,
        ]);
    }
}
