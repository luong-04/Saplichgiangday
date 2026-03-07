<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->index(['teacher_id', 'day', 'period'], 'idx_schedule_teacher_day_period');
            $table->index(['class_id', 'day', 'period'], 'idx_schedule_class_day_period');
            $table->index(['subject_id', 'class_id'], 'idx_schedule_subject_class');
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropIndex('idx_schedule_teacher_day_period');
            $table->dropIndex('idx_schedule_class_day_period');
            $table->dropIndex('idx_schedule_subject_class');
        });
    }
};
