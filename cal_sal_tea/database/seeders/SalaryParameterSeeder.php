<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SalaryParameterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seeding for payroll_parameters
        DB::table('payroll_parameters')->insert([
            [
                'base_pay_per_period' => 50000,
                'valid_from' => '2023-01-01',
                'valid_to' => '2023-12-31',
                'description' => 'Mức lương cơ bản áp dụng từ năm 2023',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'base_pay_per_period' => 55000,
                'valid_from' => '2024-01-01',
                'valid_to' => null,
                'description' => 'Mức lương cơ bản áp dụng từ năm 2024',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Seeding for class_size_coefficients
        DB::table('class_size_coefficients')->insert([
            [
                'min_students' => 0,
                'max_students' => 20,
                'coefficient' => 0.8,
                'valid_from' => '2023-01-01',
                'valid_to' => null,
                'description' => 'Lớp ít sinh viên',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'min_students' => 21,
                'max_students' => 50,
                'coefficient' => 1.0,
                'valid_from' => '2023-01-01',
                'valid_to' => null,
                'description' => 'Lớp sĩ số chuẩn',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'min_students' => 51,
                'max_students' => 100,
                'coefficient' => 1.2,
                'valid_from' => '2023-01-01',
                'valid_to' => null,
                'description' => 'Lớp đông sinh viên',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'min_students' => 101,
                'max_students' => 999,
                'coefficient' => 1.5,
                'valid_from' => '2023-01-01',
                'valid_to' => null,
                'description' => 'Lớp rất đông sinh viên',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
