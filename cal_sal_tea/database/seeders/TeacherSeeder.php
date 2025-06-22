<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('vi_VN');
        $facultyIds = DB::table('faculties')->pluck('id');
        $degreeIds = DB::table('degrees')->pluck('id');

        for ($i = 0; $i < 20; $i++) {
            DB::table('teachers')->insert([
                'teacher_code' => 'GV' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'full_name' => $faker->name,
                'date_of_birth' => $faker->date,
                'phone' => $faker->phoneNumber,
                'email' => $faker->unique()->safeEmail,
                'faculty_id' => $faker->randomElement($facultyIds),
                'degree_id' => $faker->randomElement($degreeIds),
                'is_active' => $faker->boolean(90), // 90% chance of being active
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
