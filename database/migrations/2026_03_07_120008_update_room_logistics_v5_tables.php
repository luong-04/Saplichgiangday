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
        Schema::dropIfExists('room_subject');

        Schema::table('rooms', function (Blueprint $table) {
            if (Schema::hasColumn('rooms', 'category')) {
                $table->dropColumn('category');
            }
            if (!Schema::hasColumn('rooms', 'room_category_id')) {
                $table->foreignId('room_category_id')->nullable()->constrained('room_categories')->nullOnDelete();
            }
        });

        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'preferred_room_category')) {
                $table->dropColumn('preferred_room_category');
            }
            if (!Schema::hasColumn('subjects', 'room_category_id')) {
                $table->foreignId('room_category_id')->nullable()->constrained('room_categories')->nullOnDelete();
            }
        });

        Schema::table('teachers', function (Blueprint $table) {
            if (!Schema::hasColumn('teachers', 'room_category_ids')) {
                $table->json('room_category_ids')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('room_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->string('category')->nullable();
            $table->dropForeign(['room_category_id']);
            $table->dropColumn('room_category_id');
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->string('preferred_room_category')->nullable();
            $table->dropForeign(['room_category_id']);
            $table->dropColumn('room_category_id');
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn('room_category_ids');
        });
    }
};
