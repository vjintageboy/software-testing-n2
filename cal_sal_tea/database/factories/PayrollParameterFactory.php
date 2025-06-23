<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PayrollParameter;

class PayrollParameterFactory extends Factory
{
    protected $model = PayrollParameter::class;

    public function definition(): array
    {
        return [
            // Sửa lại cho đúng với cấu trúc cuối cùng
            'base_pay_per_period' => $this->faker->numberBetween(100000, 300000),
            'valid_from' => now()->subMonth(),
            'valid_to' => now()->addYear(),
            'description' => $this->faker->sentence,
        ];
    }
}