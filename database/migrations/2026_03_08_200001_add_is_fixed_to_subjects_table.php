<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->boolean('is_fixed')->default(false)->after('type')
                ->comment('Tiết cố định (Chào cờ, Sinh hoạt,...) — không xếp tự động');
        });

        // Tự đánh dấu các môn cố định hiện có
        \App\Models\Subject::where(function ($q) {
            $q->whereRaw("LOWER(name) LIKE '%chào cờ%'")
                ->orWhereRaw("LOWER(name) LIKE '%sinh hoạt%'");
        })->update(['is_fixed' => true]);
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('is_fixed');
        });
    }
};
