<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Term;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Term>
 */
class TermFactory extends Factory
{
    protected $model = Term::class;

    public function definition(): array
    {
        $start_year = $this->faker->numberBetween(2020, 2025);
        return [
            'name' => 'Học kỳ ' . $this->faker->unique()->numberBetween(1, 20),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),

            // Dòng code được thêm vào để sửa lỗi NOT NULL
            'academic_year' => $start_year . '-' . ($start_year + 1),
        ];
    }
}