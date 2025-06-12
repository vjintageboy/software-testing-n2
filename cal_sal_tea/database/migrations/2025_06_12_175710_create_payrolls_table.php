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
    Schema::create('payrolls', function (Blueprint $table) {
        $table->id();
        
        // Foreign keys
        $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
        $table->foreignId('term_id')->constrained('terms')->onDelete('cascade');
        $table->foreignId('assignment_id')->constrained('assignments')->onDelete('cascade');
        
        $table->dateTime('calculation_date');
        $table->decimal('total_amount', 15, 2);
        
        // --- Snapshots of parameters at the time of calculation ---
        $table->decimal('base_pay_snapshot', 10, 2);
        $table->decimal('degree_coeff_snapshot', 4, 2);
        $table->decimal('course_coeff_snapshot', 4, 2);
        $table->decimal('class_coeff_snapshot', 4, 2);
        $table->integer('standard_periods_snapshot');
        $table->integer('number_of_students_snapshot');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
