<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            // Drop existing FK constraint, make nullable, re-add FK with nullOnDelete
            $table->dropForeign(['teacher_id']);
            $table->foreignId('teacher_id')->nullable()->change();
            $table->foreign('teacher_id')
                ->references('id')->on('teachers')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->foreignId('teacher_id')->nullable(false)->change();
            $table->foreign('teacher_id')
                ->references('id')->on('teachers')
                ->cascadeOnDelete();
        });
    }
};
