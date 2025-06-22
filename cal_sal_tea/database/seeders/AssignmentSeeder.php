<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class AssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $teacherIds = DB::table('teachers')->where('is_active', true)->pluck('id');
        $courseClassIds = DB::table('course_classes')->pluck('id');

        if ($teacherIds->isEmpty() || $courseClassIds->isEmpty()) {
            $this->command->info('Cannot seed assignments without teachers and course classes.');
            return;
        }

        foreach ($courseClassIds as $courseClassId) {
            // Ensure a teacher is available
            if ($teacherIds->isNotEmpty()) {
                DB::table('assignments')->insert([
                    'teacher_id' => $faker->randomElement($teacherIds),
                    'course_class_id' => $courseClassId,
                    'notes' => $faker->sentence,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
