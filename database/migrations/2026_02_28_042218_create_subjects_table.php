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
    Schema::create('subjects', function (Blueprint $table) {
        $table->id(); // Cột id 
        $table->string('name'); // Cột name 
        $table->tinyInteger('type')->comment('0=Văn hóa, 1=Thể dục, 2=Thực hành'); // Cột type 
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
