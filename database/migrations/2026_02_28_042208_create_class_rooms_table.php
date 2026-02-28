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
    Schema::create('classes', function (Blueprint $table) {
        $table->id(); // Cột id 
        $table->string('name'); // Cột name 
        $table->string('grade'); // Cột grade 
        $table->string('lookup_code')->unique(); // Cột lookup_code 
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_rooms');
    }
};
