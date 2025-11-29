<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('team_user', function (Blueprint $table) {
            if (!Schema::hasColumn('team_user', 'start_date')) {
                $table->date('start_date')->nullable()->after('role');
            }

            if (!Schema::hasColumn('team_user', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }

            if (!Schema::hasColumn('team_user', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('end_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_user', function (Blueprint $table) {
            $columns = ['start_date', 'end_date', 'is_active'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('team_user', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
