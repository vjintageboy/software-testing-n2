<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('faculties')->insert([
            [
                'name' => 'Công nghệ thông tin',
                'abbreviation' => 'CNTT',
                'description' => 'Khoa Công nghệ thông tin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kỹ thuật Phần mềm',
                'abbreviation' => 'KTPM',
                'description' => 'Khoa Kỹ thuật Phần mềm',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Khoa học Máy tính',
                'abbreviation' => 'KHMT',
                'description' => 'Khoa Khoa học Máy tính',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mạng máy tính và Truyền thông',
                'abbreviation' => 'MMT',
                'description' => 'Khoa Mạng máy tính và Truyền thông',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
