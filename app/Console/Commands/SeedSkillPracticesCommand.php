<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\ComprehensiveSkillPracticesSeeder;
use Database\Seeders\ComprehensiveSkillPracticesContinuationSeeder;
use Database\Seeders\ComprehensiveSkillPracticesFinalSeeder;

class SeedSkillPracticesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-skill-practices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seeds the comprehensive list of skill practices';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to seed comprehensive skill practices...');

        $this->info('Seeding first set of skill practices...');
        $this->call(ComprehensiveSkillPracticesSeeder::class);

        $this->info('Seeding continuation of skill practices...');
        $this->call(ComprehensiveSkillPracticesContinuationSeeder::class);

        $this->info('Seeding final set of skill practices...');
        $this->call(ComprehensiveSkillPracticesFinalSeeder::class);

        $this->info('Finished seeding all skill practices!');

        return Command::SUCCESS;
    }
}
