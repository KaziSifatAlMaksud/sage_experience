<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserSkillPractice;
use Illuminate\Support\Facades\DB;

class UpdateSelectedAt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-selected-at';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update selected_at field for existing skill practices to match created_at';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating selected_at values for existing user skill practices...');

        $count = DB::table('user_skill_practices')
            ->whereNull('selected_at')
            ->update(['selected_at' => DB::raw('created_at')]);

        $this->info("Updated {$count} records.");

        return Command::SUCCESS;
    }
}
