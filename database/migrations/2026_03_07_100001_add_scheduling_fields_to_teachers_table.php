<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->unsignedTinyInteger('max_periods_per_day')->default(5)->comment('Số tiết tối đa mỗi ngày');
            $table->json('availability')->nullable()->comment('Lịch rảnh: {"2":[1,2,3],"3":[1,2]} key=thứ, values=tiết rảnh');
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn(['max_periods_per_day', 'availability']);
        });
    }
};
