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
        Schema::create('curricula', function (Blueprint $table) {
            $table->id();
            $table->integer('grade')->comment('Khối học (VD: 10, 11, 12)');
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->integer('lessons_per_week')->default(1)->comment('Số tiết 1 tuần');
            $table->timestamps();

            // Mỗo môn học trong một khối chỉ có 1 cấu hình
            $table->unique(['grade', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curricula');
    }
};
