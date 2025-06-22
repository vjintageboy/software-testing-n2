<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('terms')->insert([
            [
                'name' => 'Học kỳ 1',
                'academic_year' => '2023-2024',
                'start_date' => '2023-09-05',
                'end_date' => '2024-01-15',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Học kỳ 2',
                'academic_year' => '2023-2024',
                'start_date' => '2024-01-22',
                'end_date' => '2024-06-30',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Học kỳ 1',
                'academic_year' => '2024-2025',
                'start_date' => '2024-09-05',
                'end_date' => '2025-01-15',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Học kỳ 2',
                'academic_year' => '2024-2025',
                'start_date' => '2025-01-22',
                'end_date' => '2025-06-30',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
