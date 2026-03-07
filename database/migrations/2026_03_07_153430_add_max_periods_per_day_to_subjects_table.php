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
        Schema::table('subjects', function (Blueprint $table) {
            $table->integer('max_periods_per_day')->default(2)->comment('Số tiết tối đa trong 1 ngày');
            $table->integer('consecutive_periods')->default(1)->comment('Số tiết liền');
        });

        if (Schema::hasColumn('subjects', 'is_double_period')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->dropColumn('is_double_period');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['max_periods_per_day', 'consecutive_periods']);
            $table->boolean('is_double_period')->default(false)->comment('Ưu tiên xếp tiết đôi liên tiếp');
        });
    }
};
