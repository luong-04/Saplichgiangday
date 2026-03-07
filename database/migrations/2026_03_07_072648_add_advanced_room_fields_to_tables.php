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
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('category')->nullable()->after('name')->comment('Loại phòng (Tin học, Lab Lý...)');
            $table->boolean('status')->default(true)->after('capacity')->comment('1: Hoạt động, 0: Bảo trì');
        });

        Schema::table('class_rooms', function (Blueprint $table) {
            $table->integer('student_count')->default(40)->after('shift')->comment('Sĩ số lớp');
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->string('preferred_room_category')->nullable()->after('type')->comment('Loại phòng ưu tiên (Tin học, Lab...)');
            $table->integer('consecutive_periods')->default(1)->after('is_double_period')->comment('Số tiết liên tiếp (1-4). Thay thế is_double_period');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('enforce_room_assignment')->default(true)->after('max_subject_days_gap')->comment('Bắt buộc chọn phòng khi gán môn thực hành');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['category', 'status']);
        });

        Schema::table('class_rooms', function (Blueprint $table) {
            $table->dropColumn('student_count');
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['preferred_room_category', 'consecutive_periods']);
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('enforce_room_assignment');
        });
    }
};
