<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::create('fixed_periods', function (Blueprint $table) {
            $table->id();
            $table->string('subject_name'); // Chào cờ, Sinh hoạt lớp
            $table->unsignedTinyInteger('day'); // 2-7
            $table->unsignedTinyInteger('period'); // 1-10
            $table->string('shift')->default('morning')->comment('morning/afternoon');
            $table->boolean('auto_assign_homeroom')->default(false)->comment('Tự gán GVCN');
            $table->timestamps();
        });

        // Seed tiết cố định mặc định
        \Illuminate\Support\Facades\DB::table('fixed_periods')->insert([
            ['subject_name' => 'Chào cờ', 'day' => 2, 'period' => 1, 'shift' => 'morning', 'auto_assign_homeroom' => false, 'created_at' => now(), 'updated_at' => now()],
            ['subject_name' => 'Chào cờ', 'day' => 2, 'period' => 6, 'shift' => 'afternoon', 'auto_assign_homeroom' => false, 'created_at' => now(), 'updated_at' => now()],
            ['subject_name' => 'Sinh hoạt lớp', 'day' => 7, 'period' => 5, 'shift' => 'morning', 'auto_assign_homeroom' => true, 'created_at' => now(), 'updated_at' => now()],
            ['subject_name' => 'Sinh hoạt lớp', 'day' => 7, 'period' => 10, 'shift' => 'afternoon', 'auto_assign_homeroom' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('fixed_periods');
    }
};
