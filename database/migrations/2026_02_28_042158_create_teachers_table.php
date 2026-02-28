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
    Schema::create('teachers', function (Blueprint $table) {
        $table->id(); // Cột id 
        $table->string('name'); // Cột name 
        $table->string('short_code')->nullable(); // Cột short_code 
        $table->string('lookup_code')->unique(); // Cột lookup_code (UNIQUE) 
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
