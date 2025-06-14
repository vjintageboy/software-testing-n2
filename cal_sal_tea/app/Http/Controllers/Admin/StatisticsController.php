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

        // 2. Dữ liệu thống kê phụ thuộc vào học kỳ được chọn
        $terms = Term::orderBy('start_date', 'desc')->get(); // Lấy tất cả các kỳ cho dropdown
        $requestedTermId = $request->input('term_id'); // ID từ form, có thể là ID số, rỗng (''), hoặc null

        $selectedTerm = null;
        $viewSelectedTermId = ''; // Giá trị sẽ được select trong dropdown ở view, rỗng nghĩa là "Tất cả"

        if ($requestedTermId === '' || $requestedTermId === null) { // Người dùng chọn "Tất cả học kỳ"
            // $selectedTerm vẫn là null
            // $viewSelectedTermId vẫn là ''
        } elseif (is_numeric($requestedTermId)) { // Người dùng chọn một kỳ cụ thể
            $term = Term::find($requestedTermId);
            if ($term) {
                $selectedTerm = $term;
                $viewSelectedTermId = $term->id;
            }
        } else { // Initial load or non-standard
            $activeTerm = Term::getActiveTerm();
            if ($activeTerm) {
                $selectedTerm = $activeTerm;
                $viewSelectedTermId = $activeTerm->id;
            }
        }
        $isAllTerms = ($selectedTerm === null);

        $facultyChartData = ['labels' => [], 'data' => []];
        $degreeChartData = ['labels' => [], 'data' => []];
        $teacherWorkload = collect();

        if ($isAllTerms) {
            $teachersPerFaculty = Faculty::withCount('teachers')->orderBy('teachers_count', 'desc')->get();
            $teachersPerDegree = Degree::withCount('teachers')->orderBy('teachers_count', 'desc')->get();
            $assignments = Assignment::with(['teacher.faculty', 'teacher.degree', 'courseClass.course'])->get();
            $widgetData['activeTermName'] = 'Tất cả học kỳ';
        } else { // Một kỳ cụ thể được chọn
            $teachersPerFaculty = Faculty::withCount(['teachers' => function ($query) use ($selectedTerm) {
                $query->whereHas('assignments', function ($assignmentQuery) use ($selectedTerm) {
                    $assignmentQuery->whereHas('courseClass', function ($courseClassQuery) use ($selectedTerm) {
                        $courseClassQuery->where('term_id', $selectedTerm->id);
                    });
                });
            }])->orderBy('teachers_count', 'desc')->get();

            $teachersPerDegree = Degree::withCount(['teachers' => function ($query) use ($selectedTerm) {
                $query->whereHas('assignments', function ($assignmentQuery) use ($selectedTerm) {
                    $assignmentQuery->whereHas('courseClass', function ($courseClassQuery) use ($selectedTerm) {
                        $courseClassQuery->where('term_id', $selectedTerm->id);
                    });
                });
            }])->orderBy('teachers_count', 'desc')->get();

            $assignments = Assignment::with(['teacher.faculty', 'teacher.degree', 'courseClass.course'])
                ->whereHas('courseClass', fn ($q) => $q->where('term_id', $selectedTerm->id))
                ->get();
            $widgetData['activeTermName'] = $selectedTerm->name;
        }

        $facultyChartData = [
            'labels' => $teachersPerFaculty->pluck('name'),
            'data' => $teachersPerFaculty->pluck('teachers_count'),
        ];
        $degreeChartData = [
            'labels' => $teachersPerDegree->pluck('abbreviation'),
            'data' => $teachersPerDegree->pluck('teachers_count'),
        ];

        // Tính toán khối lượng giảng dạy (logic này áp dụng cho $assignments đã được lọc hoặc không)
        if ($assignments->isNotEmpty()) {
            $assignmentsByTeacher = $assignments->groupBy('teacher_id');
            foreach ($assignmentsByTeacher as $teacherId => $teacherAssignments) {
                $teacher = $teacherAssignments->first()->teacher;
                if (!$teacher) continue;

                $total_hours = 0;
                $total_classes = $teacherAssignments->count();

                foreach ($teacherAssignments as $assignment) {
                    $course_class = $assignment->courseClass;
                    $course = $course_class->course;
                    // Đảm bảo các model liên quan tồn tại
                    if (!$course_class || !$course || !$teacher->degree || !$teacher->faculty) {
                        continue; // Bỏ qua nếu thiếu dữ liệu quan trọng
                    }

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
                    'faculty_name'   => $teacher->faculty->name, // Đã kiểm tra tồn tại ở trên
                    'degree_name'    => $teacher->degree->name,   // Đã kiểm tra tồn tại ở trên
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

        $widgetData['totalHoursThisTerm'] = $teacherWorkload->sum('total_hours');

        // Định dạng lại cho datatable
        // $tableData = $teacherWorkload->map(function ($item, $index) { // Bỏ đi, vì đã có trong $config
        //     return [
        //         $index + 1,
        //         $item['teacher_name'],
        //         $item['faculty_name'],
        //         $item['degree_name'],
        //         $item['total_classes'],
        //         $item['total_hours'],
        //     ];
        // })->toArray();
        // Cấu hình bảng - Đã có sẵn $tableData trong $config, không cần tạo lại
        // $config['data'] = $tableData; // Đảm bảo $config['data'] được cập nhật đúng

        // Cấu hình bảng
        $config = [
            'data' => $teacherWorkload->map(function ($item, $key) {
                return [
                    $key + 1,
                    $item['teacher_name'],
                    $item['faculty_name'],
                    $item['degree_name'],
                    $item['total_classes'],
                    $item['total_hours'],
                ];
            })->toArray(),
            'order' => [[5, 'desc']], // Sắp xếp theo cột "Giờ quy đổi" (index 5) giảm dần
            'columns' => [
                ['title' => 'STT', 'orderable' => false], // STT không sắp xếp
                ['title' => 'Giảng viên'],
                ['title' => 'Khoa'],
                ['title' => 'Học vị'],
                ['title' => 'Số lớp'],
                ['title' => 'Giờ quy đổi', 'orderable' => true], // Tổng giờ
            ],
            'paging' => true,
            'lengthChange' => true,
            'searching' => true,
            'ordering' => true,
            'info' => true,
            'responsive' => true,
            'autoWidth' => false,
            'language' => [
                'url' => asset('vendor/datatables/i18n/Vietnamese.json'),
            ],
        ];

        $dataToView = [
            'widgetData' => $widgetData,
            'facultyChartData' => $facultyChartData,
            'degreeChartData' => $degreeChartData,
            'terms' => $terms,
            'selectedTermId' => $viewSelectedTermId,
            'teacherWorkload' => $teacherWorkload,
            'workloadChartData' => $workloadChartData,
            'config' => $config,
        ];

        return view('admin.statistics.teachers', $dataToView);
    }

    public function courseStatistics(Request $request)
    {
        // 1. Dữ liệu tổng quan không phụ thuộc học kỳ
        $widgetData = [
            'totalUniqueCourses' => Course::count(),
        ];

        // 2. Dữ liệu thống kê phụ thuộc vào học kỳ được chọn
        $terms = Term::orderBy('start_date', 'desc')->get();
        $requestedTermId = $request->input('term_id');

        $selectedTerm = null;
        $viewSelectedTermId = '';

        if ($requestedTermId === '') { // "All Terms"
            // $selectedTerm remains null
        } elseif (is_numeric($requestedTermId)) {
            $term = Term::find($requestedTermId);
            if ($term) {
                $selectedTerm = $term;
                $viewSelectedTermId = $term->id;
            }
        } else { // Initial load or non-standard
            $activeTerm = Term::getActiveTerm();
            if ($activeTerm) {
                $selectedTerm = $activeTerm;
                $viewSelectedTermId = $activeTerm->id;
            }
        }
        $isAllTerms = ($selectedTerm === null);
        
        $facultyChartData = ['labels' => [], 'data' => []];
        $creditsChartData = ['labels' => [], 'data' => []];
        $courseTermStats = [];
        $totalClassesInTerm = 0;
        $totalStudentsInTerm = 0;
        $enrollmentChartData = ['labels' => [], 'data' => []];

        if ($isAllTerms) {
            $coursesPerFaculty = Faculty::withCount('courses')->orderBy('courses_count', 'desc')->get();
            $coursesByCredits = Course::query()
                ->select('credits', DB::raw('count(courses.id) as count'))
                ->groupBy('credits')
                ->orderBy('credits')
                ->get();
            $classesInTerm = CourseClass::with('course.faculty')->get();
            $widgetData['activeTermName'] = 'Tất cả học kỳ';
        } else { // Một kỳ cụ thể được chọn
            $coursesPerFaculty = Faculty::withCount(['courses' => function ($query) use ($selectedTerm) {
                $query->whereHas('courseClasses', function ($subQuery) use ($selectedTerm) {
                    $subQuery->where('term_id', $selectedTerm->id);
                });
            }])
            ->orderBy('courses_count', 'desc')
            ->get();

            $coursesByCredits = Course::whereHas('courseClasses', function ($query) use ($selectedTerm) {
                $query->where('term_id', $selectedTerm->id);
            })
            ->select('credits', DB::raw('count(DISTINCT courses.id) as count')) // Đảm bảo đếm courses.id duy nhất
            ->groupBy('credits')
            ->orderBy('credits')
            ->get();

            $classesInTerm = CourseClass::with('course.faculty')
                ->where('term_id', $selectedTerm->id)
                ->get();
            $widgetData['activeTermName'] = $selectedTerm->name;
        }

        $facultyChartData = [
            'labels' => $coursesPerFaculty->pluck('name'),
            'data' => $coursesPerFaculty->pluck('courses_count'),
        ];
        $creditsChartData = [
            'labels' => $coursesByCredits->pluck('credits')->map(fn ($c) => $c . ' tín chỉ'),
            'data' => $coursesByCredits->pluck('count'),
        ];

        if ($classesInTerm->isNotEmpty()) {
            $totalClassesInTerm = $classesInTerm->count();
            $totalStudentsInTerm = $classesInTerm->sum('number_of_students');
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

        $widgetData['totalClassesInTerm'] = $totalClassesInTerm;
        $widgetData['totalStudentsInTerm'] = $totalStudentsInTerm;

        // return view('admin.statistics.courses', compact( // Sửa tương tự
        //     'widgetData',
        //     'facultyChartData',
        //     'creditsChartData',
        //     'terms',
        //     'selectedTermId' => $viewSelectedTermId,
        //     'courseTermStats',
        //     'enrollmentChartData'
        // ));
        $dataToCourseView = [
            'widgetData',
            'facultyChartData',
            'creditsChartData',
            'terms',
            'selectedTermId' => $viewSelectedTermId,
            'courseTermStats',
            'enrollmentChartData'
        ];
        return view('admin.statistics.courses', [
            'widgetData' => $widgetData,
            'facultyChartData' => $facultyChartData,
            'creditsChartData' => $creditsChartData,
            'terms' => $terms,
            'selectedTermId' => $viewSelectedTermId,
            'courseTermStats' => $courseTermStats,
            'enrollmentChartData' => $enrollmentChartData,
        ]);
    }
}
