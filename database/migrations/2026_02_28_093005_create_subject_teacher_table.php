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
    Schema::create('subject_teacher', function (Blueprint $table) {
        $table->id();
        $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
        $table->foreignId('subject_id')->constrained()->onDelete('cascade');
    });

    Schema::table('teachers', function (Blueprint $table) {
        // Xóa ràng buộc khóa ngoại trước khi xóa cột
        $table->dropForeign(['subject_id']); 
        $table->dropColumn('subject_id');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_teacher');
    }
};
