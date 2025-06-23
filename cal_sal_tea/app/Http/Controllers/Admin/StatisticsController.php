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
use App\Models\Payroll;
use App\Models\PayrollParameter;
use PDF;

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
                if (!$teacher) {
                    continue;
                }

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
                if (!$course) {
                    continue; // Bỏ qua nếu không tìm thấy thông tin học phần
                }

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

    /**
     * Helper function to get class size coefficient.
     */
    private function getClassSizeCoefficient($numberOfStudents, $coefficients)
    {
        foreach ($coefficients as $coeff) {
            if ($numberOfStudents >= $coeff->min_students && $numberOfStudents <= $coeff->max_students) {
                return $coeff->coefficient;
            }
        }
        return 1.0; // Default coefficient
    }

    /**
     * Phương thức phụ trợ mới: Lấy hệ số sĩ số đang có hiệu lực tại một ngày cụ thể.
     *
     * @param \Illuminate\Support\Collection $coefficients Collection chứa tất cả các hệ số.
     * @param int $numberOfStudents Sĩ số lớp.
     * @param string $date Ngày cần kiểm tra (định dạng 'Y-m-d').
     * @return float
     */
    private function getActiveClassSizeCoefficient($coefficients, $numberOfStudents, $date)
    {
        $applicableCoefficients = $coefficients->filter(function ($coeff) use ($date) {
            $isValidFrom = $coeff->valid_from ? $date >= $coeff->valid_from : true;
            $isValidTo = $coeff->valid_to ? $date <= $coeff->valid_to : true;
            return $isValidFrom && $isValidTo;
        });

        foreach ($applicableCoefficients as $coeff) {
            if ($numberOfStudents >= $coeff->min_students && $numberOfStudents <= $coeff->max_students) {
                return $coeff->coefficient;
            }
        }

        return 1.0; // Hệ số mặc định nếu không tìm thấy
    }

    /**
     * Phương thức getTeacherReportData đã được cập nhật logic
     */
    private function getTeacherReportData($teacherId, $year)
    {
        $selectedTeacher = Teacher::with('degree', 'faculty')->findOrFail($teacherId);
        $payrollParameters = PayrollParameter::first();
        $allClassSizeCoefficients = ClassSizeCoefficient::all();

        // Sửa câu truy vấn để dùng quan hệ lồng nhau
        $assignments = Assignment::where('teacher_id', $teacherId)
            ->whereHas('courseClass.term', function ($query) use ($year) {
                $query->whereYear('start_date', $year);
            })
            ->with(['courseClass.term', 'courseClass.course'])
            ->get();

        // Gom nhóm và tính toán dữ liệu
        $reportDetails = $assignments->groupBy('courseClass.term.name')->map(function ($termAssignments) use ($payrollParameters, $allClassSizeCoefficients, $selectedTeacher) {
            $termTotal = 0;
            $details = $termAssignments->map(function ($assignment) use ($payrollParameters, $allClassSizeCoefficients, $selectedTeacher, &$termTotal) {
                $course = $assignment->courseClass->course;
                $courseClass = $assignment->courseClass;

                // Truy cập term thông qua courseClass
                $termStartDate = $assignment->courseClass->term->start_date;

                // Get base pay parameter for the term
                $basePayParam = PayrollParameter::where('valid_from', '<=', $termStartDate)
                    ->where(function ($query) use ($termStartDate) {
                        $query->where('valid_to', '>=', $termStartDate)
                              ->orWhereNull('valid_to');
                    })
                    ->first();

                if ($basePayParam) {
                    $basePayPerPeriod = $basePayParam->base_pay_per_period;
                    $teacherCoefficient = $selectedTeacher->degree->coefficient;
                    $courseCoefficient = $course->coefficient;
                    $standardPeriods = $course->standard_periods;

                    $classSizeCoeff = $this->getActiveClassSizeCoefficient(
                        $allClassSizeCoefficients,
                        $courseClass->number_of_students,
                        $termStartDate
                    );

                    // Calculate using the correct formula from PayrollController
                    $convertedPeriods = $standardPeriods * ($courseCoefficient + $classSizeCoeff);
                    $classAmount = $convertedPeriods * $teacherCoefficient * $basePayPerPeriod;
                    $termTotal += $classAmount;

                    return [
                        'course_name' => $course->name,
                        'class_code' => $courseClass->class_code,
                        'number_of_students' => $courseClass->number_of_students,
                        'standard_periods' => $standardPeriods,
                        'base_pay_per_period' => $basePayPerPeriod,
                        'degree_coefficient' => $teacherCoefficient,
                        'course_coefficient' => $courseCoefficient,
                        'class_size_coefficient' => $classSizeCoeff,
                        'converted_periods' => $convertedPeriods,
                        'class_amount' => $classAmount,
                    ];
                }

                // Return null if no base pay parameter found
                return null;
            })->filter(function ($detail) {
                return $detail !== null;
            });

            return [
                'term_name' => $termAssignments->first()->courseClass->term->name,
                'details' => $details,
                'term_total' => $termTotal,
            ];
        });

        $totalYearlySalary = $reportDetails->sum('term_total');

        return compact('selectedTeacher', 'year', 'reportDetails', 'totalYearlySalary', 'payrollParameters');
    }

    /**
     * UC4.1: Display teacher salary report page
     */
    public function teacherSalaryReport(Request $request)
    {
        $years = Term::select(DB::raw('DISTINCT YEAR(start_date) as year'))
            ->orderBy('year', 'desc')
            ->pluck('year');

        $selectedYear = $request->input('year');
        $reportData = null;
        $totalYearlySalary = 0;
        $faculties = Faculty::orderBy('name')->get();

        if ($request->isMethod('post') && $selectedYear) {
            $reportData = Teacher::with(['faculty', 'degree'])
                ->with(['payrolls' => function ($query) use ($selectedYear) {
                    $query->whereHas('term', function ($subQuery) use ($selectedYear) {
                        $subQuery->whereYear('start_date', $selectedYear);
                    });
                }])
                ->get()
                ->map(function ($teacher) {
                    $teacher->total_amount = $teacher->payrolls->sum('total_amount');
                    return $teacher;
                })
                ->filter(function ($teacher) {
                    return $teacher->total_amount > 0;
                })
                ->sortByDesc('total_amount');

            $totalYearlySalary = $reportData->sum('total_amount');
        }

        return view('admin.reports.teacher_salary', compact('years', 'selectedYear', 'reportData', 'totalYearlySalary', 'faculties'));
    }

    /**
     * Display detailed teacher salary report
     */
    public function teacherSalaryDetail($teacher_id, Request $request)
    {
        $year = $request->input('year');
        if (!$year) {
            return redirect()->route('admin.reports.teacher_salary')
                ->with('error', 'Vui lòng chọn năm.');
        }

        $data = $this->getTeacherReportData($teacher_id, $year);
        return view('admin.reports.teacher_salary_detail', $data);
    }

    /**
     * Export teacher salary report to PDF
     */
    public function exportTeacherSalaryPDF(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'year' => 'required|numeric'
        ]);

        $data = $this->getTeacherReportData($request->teacher_id, $request->year);

        if (!$data['reportDetails'] || $data['reportDetails']->isEmpty()) {
            return redirect()->back()->with('error', 'Không có dữ liệu để xuất hóa đơn.');
        }

        $filename = 'bang-ke-luong-' . \Illuminate\Support\Str::slug($data['selectedTeacher']->full_name) . '-' . $data['year'] . '.pdf';

        $pdf = PDF::loadView('admin.reports.teacher_salary_pdf', $data);

        return $pdf->stream($filename);
    }

    /**
     * UC4.2: Báo cáo tiền dạy của giáo viên theo khoa
     */
    public function facultySalaryReport(Request $request)
    {
        $years = Term::select(DB::raw('DISTINCT YEAR(start_date) as year'))
            ->orderBy('year', 'desc')
            ->pluck('year');

        $selectedYear = $request->input('year');
        $reportData = null;
        $totalSalary = 0;
        $averageSalary = 0;
        $chartLabels = collect();
        $chartData = collect();
        $top5Labels = collect();
        $top5Data = collect();

        if ($request->isMethod('post') && $selectedYear) {
            $reportData = Faculty::withCount(['teachers' => function ($query) use ($selectedYear) {
                    $query->whereHas('payrolls.term', function ($subQuery) use ($selectedYear) {
                        $subQuery->whereYear('start_date', $selectedYear);
                    });
            }])
                ->with(['teachers.payrolls' => function ($query) use ($selectedYear) {
                    $query->whereHas('term', function ($subQuery) use ($selectedYear) {
                        $subQuery->whereYear('start_date', $selectedYear);
                    });
                }])
                ->get()
                ->map(function ($faculty) {
                    $faculty->total_salary = $faculty->teachers->reduce(function ($carry, $teacher) {
                        return $carry + $teacher->payrolls->sum('total_amount');
                    }, 0);
                    $faculty->average_salary = $faculty->teachers_count > 0 ?
                        $faculty->total_salary / $faculty->teachers_count : 0;
                    return $faculty;
                })
                ->filter(function ($faculty) {
                    return $faculty->total_salary > 0;
                })
                ->sortByDesc('total_salary');

            $totalSalary = $reportData->sum('total_salary');
            $averageSalary = $reportData->avg('average_salary');

            // Chuẩn bị dữ liệu cho biểu đồ tròn
            $chartLabels = $reportData->pluck('name');
            $chartData = $reportData->pluck('total_salary');

            // Chuẩn bị dữ liệu cho biểu đồ top 5
            $top5Labels = $reportData->take(5)->pluck('name');
            $top5Data = $reportData->take(5)->pluck('total_salary');
        }

        return view('admin.reports.faculty_salary', compact(
            'years',
            'selectedYear',
            'reportData',
            'totalSalary',
            'averageSalary',
            'chartLabels',
            'chartData',
            'top5Labels',
            'top5Data'
        ));
    }

    /**
     * Hiển thị chi tiết lương của một khoa
     */
    public function facultySalaryDetail(Request $request, Faculty $faculty)
    {
        $selectedYear = $request->input('year');
        if (!$selectedYear) {
            return redirect()->route('admin.reports.faculty_salary')
                ->with('error', 'Vui lòng chọn năm để xem chi tiết.');
        }

        // Lấy danh sách giáo viên của khoa có lương trong năm
        $teachers = $faculty->teachers()
            ->whereHas('payrolls.term', function ($query) use ($selectedYear) {
                $query->whereYear('start_date', $selectedYear);
            })
            ->with(['payrolls' => function ($query) use ($selectedYear) {
                $query->whereHas('term', function ($subQuery) use ($selectedYear) {
                    $subQuery->whereYear('start_date', $selectedYear);
                });
            }])
            ->get()
            ->map(function ($teacher) {
                $teacher->total_salary = $teacher->payrolls->sum('total_amount');
                return $teacher;
            })
            ->sortByDesc('total_salary');

        // Tính toán các thống kê
        $totalSalary = $teachers->sum('total_salary');
        $averageSalary = $teachers->avg('total_salary');
        $teachersCount = $teachers->count();
        $highestSalary = $teachers->max('total_salary');

        // Chuẩn bị dữ liệu cho biểu đồ phân bố lương
        $salaryRanges = [
            '0-5M' => 5000000,
            '5M-10M' => 10000000,
            '10M-15M' => 15000000,
            '15M-20M' => 20000000,
            '20M+' => PHP_FLOAT_MAX
        ];

        $distributionData = collect();
        $distributionLabels = collect();

        foreach ($salaryRanges as $label => $maxValue) {
            $count = $teachers->filter(function ($teacher) use ($maxValue, $label, $salaryRanges) {
                if ($label === '0-5M') {
                    return $teacher->total_salary <= $maxValue;
                } elseif ($label === '20M+') {
                    return $teacher->total_salary > 20000000;
                } else {
                    $keys = array_keys($salaryRanges);
                    $currentIndex = array_search($label, $keys);
                    $prevMax = $salaryRanges[$keys[$currentIndex - 1]];
                    return $teacher->total_salary > $prevMax && $teacher->total_salary <= $maxValue;
                }
            })->count();

            $distributionLabels->push($label);
            $distributionData->push($count);
        }

        return view('admin.reports.faculty_salary_detail', compact(
            'faculty',
            'selectedYear',
            'teachers',
            'totalSalary',
            'averageSalary',
            'teachersCount',
            'highestSalary',
            'distributionLabels',
            'distributionData'
        ));
    }

    /**
     * UC4.3: Báo cáo tiền dạy của giáo viên toàn trường gộp vào khoa
     */
}
