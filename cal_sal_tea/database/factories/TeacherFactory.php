<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Teacher;
use App\Models\Faculty;
use App\Models\Degree;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Teacher>
 */
class TeacherFactory extends Factory
{
    protected $model = Teacher::class;

    public function definition(): array
    {
        return [
            // Sửa 'name' -> 'full_name' cho đúng với migration
            'full_name' => $this->faker->name(),
            
            // Thêm 'teacher_code' là cột bắt buộc và duy nhất
            'teacher_code' => $this->faker->unique()->bothify('GV###???'), // Ví dụ: GV123ABC

            'faculty_id' => Faculty::factory(),
            'degree_id' => Degree::factory(),
        ];
    }
}