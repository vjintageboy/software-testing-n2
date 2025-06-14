<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Faculty;
use App\Models\Degree;
use App\Models\Term;
use App\Models\Assignment;
use App\Models\ClassSizeCoefficient;
use Illuminate\Support\Collection;
use App\Models\Course;
use App\Models\CourseClass;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    /**
     * Hiển thị trang thống kê giáo viên.
     */
    public function teacherStatistics(Request $request)
    {
        // 1. Dữ liệu tổng quan không phụ thuộc học kỳ
        $widgetData = [
            'totalTeachers' => Teacher::count(),
            'totalFaculties' => Faculty::count(),
            'totalDegrees' => Degree::count(),
        ];

        // Thống kê số lượng giáo viên theo khoa
        $teachersPerFaculty = Faculty::withCount('teachers')->orderBy('teachers_count', 'desc')->get();
        $facultyChartData = [
            'labels' => $teachersPerFaculty->pluck('name'),
            'data' => $teachersPerFaculty->pluck('teachers_count'),
        ];

        // Thống kê số lượng giáo viên theo học vị
        $teachersPerDegree = Degree::withCount('teachers')->orderBy('teachers_count', 'desc')->get();
        $degreeChartData = [
            'labels' => $teachersPerDegree->pluck('abbreviation'),
            'data' => $teachersPerDegree->pluck('teachers_count'),
        ];

        // 2. Dữ liệu thống kê phụ thuộc vào học kỳ được chọn
        $terms = Term::orderBy('start_date', 'desc')->get();
        $selectedTermId = $request->input('term_id', Term::getActiveTerm()->id ?? ($terms->first()?->id));
        $selectedTerm = Term::find($selectedTermId);

        $teacherWorkload = collect();

        if ($selectedTerm) {
            $assignments = Assignment::with(['teacher.faculty', 'teacher.degree', 'courseClass.course'])
                ->whereHas('courseClass', fn ($q) => $q->where('term_id', $selectedTerm->id))
                ->get();

            $assignmentsByTeacher = $assignments->groupBy('teacher_id');

            foreach ($assignmentsByTeacher as $teacherId => $teacherAssignments) {
                $teacher = $teacherAssignments->first()->teacher;
                $total_hours = 0;
                $total_classes = $teacherAssignments->count();

                foreach ($teacherAssignments as $assignment) {
                    $course_class = $assignment->courseClass;
                    $course = $course_class->course;
                    $num_students = $course_class->number_of_students;

                    $class_size_rule = ClassSizeCoefficient::where('min_students', '<=', $num_students)
                        ->where(function ($query) use ($num_students) {
                            $query->where('max_students', '>=', $num_students)
                                  ->orWhereNull('max_students');
                        })
                        ->orderBy('min_students', 'desc')
                        ->first();

                    $class_coefficient_value = $class_size_rule?->coefficient ?? 0.0;
                    $standard_periods = $course->standard_periods;
                    $course_coefficient_value = $course->coefficient;
                    $teacher_degree_coeff = $teacher->degree->coefficient;

                    $converted_periods_for_class = $standard_periods * ($course_coefficient_value + $class_coefficient_value);
                    $hours_for_assignment = $converted_periods_for_class * $teacher_degree_coeff;

                    $total_hours += $hours_for_assignment;
                }

                $teacherWorkload->push([
                    'teacher_id'     => $teacher->id,
                    'teacher_name'   => $teacher->full_name,
                    'faculty_name'   => $teacher->faculty->name,
                    'degree_name'    => $teacher->degree->name,
                    'total_classes'  => $total_classes,
                    'total_hours'    => round($total_hours, 2),
                ]);
            }
        }

        $teacherWorkload = $teacherWorkload->sortByDesc('total_hours')->values();

        // Dữ liệu biểu đồ khối lượng giảng dạy (Top 15)
        $topTeachersByHours = $teacherWorkload->take(15);
        $workloadChartData = [
            'labels' => $topTeachersByHours->pluck('teacher_name'),
            'data'   => $topTeachersByHours->pluck('total_hours'),
        ];

        $widgetData['activeTermName'] = $selectedTerm?->name ?? 'Chưa có dữ liệu';
        $widgetData['totalHoursThisTerm'] = $teacherWorkload->sum('total_hours');

        // Định dạng lại cho datatable
        $tableData = $teacherWorkload->map(function ($item, $index) {
            return [
                $index + 1,
                $item['teacher_name'],
                $item['faculty_name'],
                $item['degree_name'],
                $item['total_classes'],
                $item['total_hours'],
            ];
        })->toArray();

        // Cấu hình bảng
        $config = [
            'data' => $tableData,
            'columns' => [
                ['title' => 'STT'],
                ['title' => 'Giảng viên'],
                ['title' => 'Khoa'],
                ['title' => 'Học vị'],
                ['title' => 'Số lớp'],
                ['title' => 'Giờ quy đổi'],
            ],
            'paging' => true,
            'lengthChange' => true,
            'searching' => true,
            'ordering' => true,
            'info' => true,
            'responsive' => true,
            'language' => ['url' => asset('vendor/datatables/i18n/Vietnamese.json')],
        ];

        return view('admin.statistics.teachers', compact(
            'widgetData',
            'facultyChartData',
            'degreeChartData',
            'terms',
            'selectedTermId',
            'teacherWorkload',
            'workloadChartData',
            'config'
        ));
    }
    public function courseStatistics(Request $request)
{
    // 1. Dữ liệu tổng quan không phụ thuộc học kỳ
    $widgetData = [
        'totalUniqueCourses' => Course::count(),
    ];



    // 2. Dữ liệu thống kê phụ thuộc vào học kỳ được chọn
    $terms = Term::orderBy('start_date', 'desc')->get();
    $selectedTermId = $request->input('term_id', Term::getActiveTerm()->id ?? ($terms->first() ? $terms->first()->id : null));
    $selectedTerm = Term::find($selectedTermId);

        
    $facultyChartData = ['labels' => [], 'data' => []];
    $creditsChartData = ['labels' => [], 'data' => []];
    $courseTermStats = [];
    $totalClassesInTerm = 0;
    $totalStudentsInTerm = 0;
    $enrollmentChartData = ['labels' => [], 'data' => []];

    if ($selectedTerm) {
        // Thống kê số lượng học phần theo từng khoa (THEO KỲ)
        // Đếm số lượng học phần *khác nhau* của mỗi khoa có lớp trong kỳ đã chọn
        $coursesPerFaculty = Faculty::withCount(['courses' => function ($query) use ($selectedTerm) {
            $query->whereHas('courseClasses', function ($subQuery) use ($selectedTerm) {
                $subQuery->where('term_id', $selectedTerm->id);
            });
        }])
        ->orderBy('courses_count', 'desc')
        ->get();
        $facultyChartData = [
            'labels' => $coursesPerFaculty->pluck('name'),
            'data' => $coursesPerFaculty->pluck('courses_count'),
        ];

        // Thống kê phân bố học phần theo số tín chỉ (THEO KỲ)
        // Đếm số lượng học phần *khác nhau* theo từng mức tín chỉ, mà các học phần đó có lớp trong kỳ đã chọn
        $coursesByCredits = Course::whereHas('courseClasses', function ($query) use ($selectedTerm) {
            $query->where('term_id', $selectedTerm->id);
        })
        ->select('credits', DB::raw('count(courses.id) as count')) // Đảm bảo đếm courses.id
        ->groupBy('credits')
        ->orderBy('credits')
        ->get();
        $creditsChartData = [
            'labels' => $coursesByCredits->pluck('credits')->map(fn ($c) => $c . ' tín chỉ'),
            'data' => $coursesByCredits->pluck('count'),
        ];
        // Lấy tất cả lớp học phần trong kỳ
        $classesInTerm = CourseClass::with('course.faculty')
            ->where('term_id', $selectedTerm->id)
            ->get();
        
        $totalClassesInTerm = $classesInTerm->count();
        $totalStudentsInTerm = $classesInTerm->sum('number_of_students');
        
        // Nhóm các lớp theo mã học phần (course_id)
        $classesByCourse = $classesInTerm->groupBy('course_id');
        
        foreach ($classesByCourse as $courseId => $classes) {
            $course = $classes->first()->course;
            if (!$course) continue; // Bỏ qua nếu không tìm thấy thông tin học phần

            $totalStudents = $classes->sum('number_of_students');
            
            $courseTermStats[] = [
                'course_name' => $course->name,
                'course_code' => $course->course_code,
                'faculty_name' => optional($course->faculty)->name ?? 'Chưa xác định',
                'class_count' => $classes->count(),
                'total_students' => $totalStudents,
            ];
        }
    }
    
    // Sắp xếp học phần theo tổng sinh viên đăng ký giảm dần
    $courseTermStats = collect($courseTermStats)->sortByDesc('total_students')->values();
    
    // Dữ liệu cho biểu đồ top học phần
    $topCoursesByEnrollment = $courseTermStats->take(15);
    $enrollmentChartData = [
         'labels' => $topCoursesByEnrollment->pluck('course_name'),
         'data' => $topCoursesByEnrollment->pluck('total_students'),
    ];

    $widgetData['activeTermName'] = $selectedTerm ? $selectedTerm->name : 'N/A';
    $widgetData['totalClassesInTerm'] = $totalClassesInTerm;
    $widgetData['totalStudentsInTerm'] = $totalStudentsInTerm;

    return view('admin.statistics.courses', compact(
        'widgetData',
        'facultyChartData',
        'creditsChartData',
        'terms',
        'selectedTermId',
        'courseTermStats',
        'enrollmentChartData'
    ));
}
}
