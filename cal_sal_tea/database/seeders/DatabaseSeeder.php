<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('assignments')->truncate();
        DB::table('course_classes')->truncate();
        DB::table('courses')->truncate();
        DB::table('teachers')->truncate();
        DB::table('faculties')->truncate();
        DB::table('degrees')->truncate();
        DB::table('terms')->truncate();
        DB::table('payroll_parameters')->truncate();
        DB::table('class_size_coefficients')->truncate();

        Schema::enableForeignKeyConstraints();

        // User::factory(10)->create();
        $this->call([
            FacultySeeder::class,
            DegreeSeeder::class,
            TeacherSeeder::class,
            TermSeeder::class,
            CourseSeeder::class,
            CourseClassSeeder::class,
            AssignmentSeeder::class,
            SalaryParameterSeeder::class,
        ]);
    }
}
