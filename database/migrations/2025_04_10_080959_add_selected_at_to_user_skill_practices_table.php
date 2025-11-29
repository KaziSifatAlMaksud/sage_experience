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
        Schema::table('user_skill_practices', function (Blueprint $table) {
            $table->timestamp('selected_at')->nullable()->after('practice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_skill_practices', function (Blueprint $table) {
            $table->dropColumn('selected_at');
        });
    }
};
