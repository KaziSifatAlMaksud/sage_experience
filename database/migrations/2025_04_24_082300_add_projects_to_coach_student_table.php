<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('coach_student', function (Blueprint $table) {
            if (!Schema::hasColumn('coach_student', 'projects')) {
                $table->json('projects')->nullable()->after('notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('coach_student', function (Blueprint $table) {
            if (Schema::hasColumn('coach_student', 'projects')) {
                $table->dropColumn('projects');
            }
        });
    }
};
