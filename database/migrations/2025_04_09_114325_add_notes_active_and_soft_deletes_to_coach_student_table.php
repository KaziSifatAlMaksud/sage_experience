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
        Schema::table('coach_student', function (Blueprint $table) {
            if (!Schema::hasColumn('coach_student', 'active')) {
                $table->boolean('active')->default(true);
            }
            if (!Schema::hasColumn('coach_student', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coach_student', function (Blueprint $table) {
            if (Schema::hasColumn('coach_student', 'active')) {
                $table->dropColumn('active');
            }
            if (Schema::hasColumn('coach_student', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
