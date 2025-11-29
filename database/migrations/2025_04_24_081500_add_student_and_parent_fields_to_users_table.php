<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'student_phone')) {
                $table->string('student_phone')->nullable();
            }
            if (!Schema::hasColumn('users', 'student_school')) {
                $table->string('student_school')->nullable();
            }
            if (!Schema::hasColumn('users', 'parent1_name')) {
                $table->string('parent1_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'parent1_contact')) {
                $table->string('parent1_contact')->nullable();
            }
            if (!Schema::hasColumn('users', 'parent2_name')) {
                $table->string('parent2_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'parent2_contact')) {
                $table->string('parent2_contact')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'student_phone',
                'student_school',
                'parent1_name',
                'parent1_contact',
                'parent2_name',
                'parent2_contact',
            ]);
        });
    }
};
