<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');
            $table->string('label')->nullable()->comment('Mô tả hiển thị cho Admin');
            $table->string('group')->default('general')->comment('Nhóm cấu hình');
            $table->timestamps();
        });

        // Seed dữ liệu mặc định
        DB::table('settings')->insert([
            ['key' => 'periods_per_day', 'value' => '10', 'label' => 'Số tiết mỗi ngày', 'group' => 'timetable', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'days_start', 'value' => '2', 'label' => 'Ngày bắt đầu (2=Thứ 2)', 'group' => 'timetable', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'days_end', 'value' => '7', 'label' => 'Ngày kết thúc (7=Thứ 7)', 'group' => 'timetable', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'lunch_after_period', 'value' => '5', 'label' => 'Nghỉ trưa sau tiết số', 'group' => 'timetable', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'max_consecutive_periods', 'value' => '4', 'label' => 'Tối đa tiết liên tiếp GV dạy', 'group' => 'constraint', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'max_gap_periods', 'value' => '2', 'label' => 'Tối đa tiết trống giữa các ca', 'group' => 'constraint', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
