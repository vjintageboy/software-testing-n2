<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Degree;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Degree>
 */
class DegreeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Degree::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->jobTitle();
        return [
            'name' => $name,
            'abbreviation' => strtoupper(substr($name, 0, 3)),
            
            // Dòng code được thêm vào để sửa lỗi NOT NULL
           'coefficient' => 1.0,
        ];
    }
}