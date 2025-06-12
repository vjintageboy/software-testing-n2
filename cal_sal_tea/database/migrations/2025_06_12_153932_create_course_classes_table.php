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
    // Giữ nguyên tên bảng là 'course_classes'
    Schema::create('course_classes', function (Blueprint $table) {
        $table->id();
        $table->string('class_code')->unique();
        $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
        $table->foreignId('term_id')->constrained('terms')->onDelete('cascade');
        $table->integer('number_of_students')->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_classes');
    }
};
