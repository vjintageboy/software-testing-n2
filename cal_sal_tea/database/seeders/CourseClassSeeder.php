<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CourseClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $courses = DB::table('courses')->get(['id', 'course_code']);
        $termIds = DB::table('terms')->pluck('id');

        // Ensure we have courses and terms to create classes for
        if ($courses->isEmpty() || $termIds->isEmpty()) {
            $this->command->info('Cannot seed course classes without courses and terms.');
            return;
        }

        $courseClasses = [];
        for ($i = 0; $i < 50; $i++) {
            $course = $faker->randomElement($courses);
            $termId = $faker->randomElement($termIds);

            // Using $i ensures the class code is unique within this seeder execution.
            $classCode = $course->course_code . '-K' . $termId . '-N' . ($i + 1);

            $courseClasses[] = [
                'class_code' => $classCode,
                'course_id' => $course->id,
                'term_id' => $termId,
                'number_of_students' => $faker->numberBetween(20, 100),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('course_classes')->insert($courseClasses);
    }
}
