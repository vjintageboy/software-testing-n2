<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('vi_VN');
        $facultyIds = DB::table('faculties')->pluck('id');

        $courses = [
            'Nhập môn Lập trình', 'Lập trình Hướng đối tượng', 'Cấu trúc dữ liệu và giải thuật',
            'Cơ sở dữ liệu', 'Mạng máy tính', 'Hệ điều hành', 'Kiến trúc máy tính',
            'Công nghệ phần mềm', 'Phân tích và thiết kế hệ thống', 'An toàn thông tin',
            'Trí tuệ nhân tạo', 'Học máy', 'Khai phá dữ liệu', 'Xử lý ngôn ngữ tự nhiên',
            'Thị giác máy tính', 'Lập trình web', 'Lập trình di động', 'Kiểm thử phần mềm',
            'Quản lý dự án phần mềm', 'Phát triển game', 'Thực tại ảo và tăng cường',
            'Điện toán đám mây', 'Internet of Things (IoT)', 'Blockchain', 'Big Data',
            'Xử lý ảnh', 'Tin học cơ sở', 'Toán rời rạc', 'Xác suất thống kê', 'Giải tích'
        ];

        foreach ($courses as $index => $courseName) {
            DB::table('courses')->insert([
                'faculty_id' => $faker->randomElement($facultyIds),
                'course_code' => 'CSE' . str_pad($index + 101, 3, '0', STR_PAD_LEFT),
                'name' => $courseName,
                'credits' => $faker->numberBetween(2, 4),
                'standard_periods' => $faker->randomElement([30, 45, 60]),
                'coefficient' => $faker->randomFloat(2, 1, 1.5),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
