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
    Schema::table('teachers', function (Blueprint $table) {
        $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete(); // Môn dạy chuyên môn
        $table->integer('quota')->default(17); // Định mức tiết dạy/tuần
        $table->foreignId('homeroom_class_id')->nullable()->constrained('classes')->nullOnDelete(); // Lớp chủ nhiệm
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            //
        });
    }
};
