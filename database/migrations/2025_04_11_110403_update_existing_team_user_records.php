<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all existing records to set:
        // - start_date to the created_at date or now() if created_at is null
        // - is_active to true (students can be active in multiple teams)
        DB::table('team_user')
            ->whereNull('start_date')
            ->update([
                'start_date' => DB::raw('IFNULL(created_at, NOW())'),
                'is_active' => true
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration needed as we're just setting initial values
    }
};
