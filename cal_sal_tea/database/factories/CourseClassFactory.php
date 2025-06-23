<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CourseClass;
use App\Models\Course;
use App\Models\Term;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseClass>
 */
class CourseClassFactory extends Factory
{
    protected $model = CourseClass::class;

    public function definition()
   {
       return [
           'course_id' => 1,
           'term_id' => 1,
           'number_of_students' => $this->faker->numberBetween(10, 100),
           'class_code' => $this->faker->unique()->bothify('L###'), // Thêm dòng này
       ];
   }
}