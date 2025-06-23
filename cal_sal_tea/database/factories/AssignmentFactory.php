<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Assignment;
use App\Models\Teacher;
use App\Models\CourseClass;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assignment>
 */
class AssignmentFactory extends Factory
{
    protected $model = Assignment::class;

    public function definition(): array
    {
        return [
            'teacher_id' => Teacher::factory(),
            'course_class_id' => CourseClass::factory(),
            // 'lecturing_hours' => $this->faker->numberBetween(15, 45),
            // 'assisting_hours' => $this->faker->numberBetween(0, 30),
        ];
    }
}