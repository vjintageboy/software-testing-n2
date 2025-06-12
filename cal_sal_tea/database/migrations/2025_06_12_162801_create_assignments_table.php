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
    Schema::create('assignments', function (Blueprint $table) {
        $table->id();
        
        // Foreign keys to other tables
        $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
        $table->foreignId('course_class_id')->constrained('course_classes')->onDelete('cascade')->unique();
        
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
