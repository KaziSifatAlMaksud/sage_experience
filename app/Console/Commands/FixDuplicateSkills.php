<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Skill;
use App\Models\Practice;

class FixDuplicateSkills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skills:fix-duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detect and fix duplicate skills in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for duplicate skills...');

        // Get all skills grouped by name and skill_area_id
        $duplicates = DB::table('skills')
            ->select('name', 'skill_area_id', DB::raw('COUNT(*) as count'), DB::raw('GROUP_CONCAT(id) as ids'))
            ->groupBy('name', 'skill_area_id')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('No duplicate skills found!');
            return 0;
        }

        $this->info('Found ' . $duplicates->count() . ' sets of duplicate skills.');

        $this->table(
            ['Skill Name', 'Area ID', 'Count', 'IDs'],
            $duplicates->map(function($item) {
                return [
                    'name' => $item->name,
                    'area_id' => $item->skill_area_id,
                    'count' => $item->count,
                    'ids' => $item->ids
                ];
            })
        );

        if ($this->confirm('Do you want to fix these duplicates?')) {
            $fixed = 0;

            foreach ($duplicates as $duplicate) {
                $ids = explode(',', $duplicate->ids);

                // Keep the first ID, remove others
                $keepId = array_shift($ids);

                $this->info("Keeping skill ID: $keepId, removing: " . implode(',', $ids));

                // Find any practices associated with the duplicate skills and move them to the kept skill
                foreach ($ids as $removeId) {
                    // Update any practices to point to the kept skill
                    $practiceCount = Practice::where('skill_id', $removeId)->update([
                        'skill_id' => $keepId
                    ]);

                    $this->info("Updated $practiceCount practices from skill ID $removeId to $keepId");

                    // Delete the duplicate skill
                    DB::table('skills')->where('id', $removeId)->delete();
                    $fixed++;
                }
            }

            $this->info("Fixed $fixed duplicate skills.");
            return 0;
        }

        $this->info('No changes were made.');
        return 0;
    }
}
