<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ClassSizeCoefficient;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClassSizeCoefficient>
 */
class ClassSizeCoefficientFactory extends Factory
{
    protected $model = ClassSizeCoefficient::class;

    public function definition(): array
    {
        return [
            'min_students' => $this->faker->numberBetween(1, 50),
            'max_students' => $this->faker->numberBetween(51, 100),
            'coefficient' => $this->faker->randomFloat(1, 1, 2),
            // 'version' => 1,
        ];
    }
}