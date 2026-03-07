<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->unsignedTinyInteger('lessons_per_week')->default(1)->comment('Số tiết mỗi tuần');
            $table->unsignedTinyInteger('max_lessons_per_day')->default(1)->comment('Số tiết tối đa mỗi ngày');
            $table->boolean('is_double_period')->default(false)->comment('Ưu tiên xếp tiết đôi liên tiếp');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['lessons_per_week', 'max_lessons_per_day', 'is_double_period']);
        });
    }
};
