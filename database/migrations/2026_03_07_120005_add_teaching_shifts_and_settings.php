<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration 
{
    public function up(): void
    {
        // Thêm teaching_shifts cho giáo viên
        Schema::table('teachers', function (Blueprint $table) {
            $table->json('teaching_shifts')->nullable()->comment('Buổi đăng ký dạy: ["morning","afternoon"]');
        });

        // Thêm settings mới
        DB::table('settings')->insert([
            ['key' => 'morning_start', 'value' => '1', 'label' => 'Tiết bắt đầu buổi sáng', 'group' => 'shift', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'morning_end', 'value' => '5', 'label' => 'Tiết kết thúc buổi sáng', 'group' => 'shift', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'afternoon_start', 'value' => '6', 'label' => 'Tiết bắt đầu buổi chiều', 'group' => 'shift', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'afternoon_end', 'value' => '10', 'label' => 'Tiết kết thúc buổi chiều', 'group' => 'shift', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'max_subject_days_gap', 'value' => '1', 'label' => 'Max ngày trống giữa 2 tiết cùng môn', 'group' => 'constraint', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn('teaching_shifts');
        });

        DB::table('settings')->whereIn('key', [
            'morning_start', 'morning_end', 'afternoon_start', 'afternoon_end', 'max_subject_days_gap'
        ])->delete();
    }
};
