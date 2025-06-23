<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Course;
use App\Models\Faculty;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'credits' => $this->faker->numberBetween(1, 5),
            'faculty_id' => 1,
            'course_code' => $this->faker->unique()->bothify('MH###'),
            'standard_periods' => $this->faker->numberBetween(10, 60), // Thêm dòng này
        ];
    }
}