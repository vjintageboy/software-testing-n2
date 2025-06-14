<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Term;
use App\Models\Assignment;
use App\Models\ClassSizeCoefficient;
use App\Models\PayrollParameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Teacher;
use App\Models\Semester;
use App\Models\TeachingHour;

class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $term_id = $request->input('term_id');
        $terms = Term::orderBy('start_date', 'desc')->get();

        $query = Payroll::query()
            ->select('term_id')
            ->selectRaw('COUNT(*) as total_records')
            ->selectRaw('SUM(total_amount) as total_sum')
            ->selectRaw('MAX(calculation_date) as last_calculated')
            ->groupBy('term_id')
            ->orderBy('last_calculated', 'desc');

        // Chỉ áp dụng điều kiện lọc khi term_id là số và lớn hơn 0
        if (is_numeric($term_id) && $term_id > 0) {
            $query->where('term_id', $term_id);
        }

        $payrollSummaries = $query->paginate(10);

        return view('admin.payrolls.index', compact('payrollSummaries', 'terms', 'term_id'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $terms = Term::orderBy('start_date', 'desc')->get();
        return view('admin.payrolls.create', compact('terms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            \Log::info('Starting payroll calculation', [
                'request' => $request->all()
            ]);

            $request->validate([
                'term_id' => 'required|exists:terms,id'
            ]);

            $term = Term::findOrFail($request->term_id);
            \Log::info('Found term', ['term' => $term->toArray()]);

            // Lấy ngày bắt đầu của học kỳ để làm ngày tham chiếu cho tất cả các truy vấn
            $referenceDate = $term->start_date;

            // Lấy tất cả các phân công trong kì học này
            $assignments = Assignment::whereHas('courseClass', function($query) use ($term) {
                $query->where('term_id', $term->id);
            })->with(['teacher.degree', 'courseClass.course'])->get();

            if ($assignments->isEmpty()) {
                \Log::warning('No assignments found for term', ['term_id' => $term->id]);
                return redirect()->back()->with('error', 'Kì học này không có phân công nào để tính lương.');
            }

            \Log::info('Found assignments', [
                'count' => $assignments->count(),
                'assignments' => $assignments->toArray()
            ]);

            // Lấy đơn giá tiết dạy
            $basePayParam = PayrollParameter::where('valid_from', '<=', $referenceDate)
                ->where(function ($query) use ($referenceDate) {
                    $query->where('valid_to', '>=', $referenceDate)
                          ->orWhereNull('valid_to');
                })
                ->first();

            if (!$basePayParam) {
                \Log::warning('No base pay parameter found', [
                    'reference_date' => $referenceDate
                ]);
                return redirect()->back()->with('error', 'Chưa có đơn giá nào được áp dụng cho kì học này. Vui lòng thêm trong mục "Đơn giá Tiết dạy".');
            }

            \Log::info('Found base pay parameter', ['base_pay_param' => $basePayParam->toArray()]);

            $basePayPerPeriod = $basePayParam->base_pay_per_period;

            // Lấy hệ số quy mô lớp học
            $classSizeCoefficient = ClassSizeCoefficient::where('valid_from', '<=', $referenceDate)
                ->where(function ($query) use ($referenceDate) {
                    $query->where('valid_to', '>=', $referenceDate)
                          ->orWhereNull('valid_to');
                })
                ->first();

            if (!$classSizeCoefficient) {
                \Log::warning('No class size coefficient found', [
                    'reference_date' => $referenceDate
                ]);
                return redirect()->back()->with('error', 'Chưa có hệ số quy mô lớp học nào được áp dụng cho kì học này. Vui lòng thêm trong mục "Hệ số Quy mô Lớp học".');
            }

            \Log::info('Found class size coefficient', ['class_size_coefficient' => $classSizeCoefficient->toArray()]);

            DB::transaction(function () use ($assignments, $term, $basePayPerPeriod, $classSizeCoefficient, $referenceDate) {
                // Xóa các bảng lương cũ của kì này để tính lại
                Payroll::where('term_id', $term->id)->delete();

                foreach ($assignments as $assignment) {
                    // Lấy các giá trị cần thiết
                    $teacherCoefficient = $assignment->teacher->degree->coefficient; // hệ_số_giáo_viên
                    $standardPeriods = $assignment->courseClass->course->standard_periods; // Số tiết thực tế
                    $courseCoefficient = $assignment->courseClass->course->coefficient; // hệ_số_học_phần
                    
                    // Tính hệ số sĩ số
                    $studentCount = $assignment->courseClass->number_of_students;
                    $classCoefficient = 0.0;
                    
                    if ($studentCount <= $classSizeCoefficient->min_students) {
                        $classCoefficient = $classSizeCoefficient->coefficient;
                    } elseif ($studentCount <= $classSizeCoefficient->max_students) {
                        $classCoefficient = $classSizeCoefficient->coefficient * 2;
                    } else {
                        $classCoefficient = $classSizeCoefficient->coefficient * 3;
                    }

                    // Tính số tiết quy đổi
                    $convertedPeriods = $standardPeriods * ($courseCoefficient + $classCoefficient);

                    // Tính thành tiền cuối cùng
                    $totalAmount = $convertedPeriods * $teacherCoefficient * $basePayPerPeriod;

                    // Tạo bản ghi payroll với snapshot
                    Payroll::create([
                        'teacher_id' => $assignment->teacher_id,
                        'term_id' => $term->id,
                        'assignment_id' => $assignment->id,
                        'calculation_date' => Carbon::now(),
                        'total_amount' => $totalAmount,
                        'base_pay_snapshot' => $basePayPerPeriod,
                        'degree_coeff_snapshot' => $teacherCoefficient,
                        'course_coeff_snapshot' => $courseCoefficient,
                        'class_coeff_snapshot' => $classCoefficient,
                        'standard_periods_snapshot' => $standardPeriods,
                        'number_of_students_snapshot' => $studentCount,
                    ]);
                }
            });

            return redirect()->route('payrolls.index')
                ->with('success', 'Đã tính toán và lưu bảng lương cho ' . $term->name . ' - ' . $term->academic_year . ' thành công!');
        } catch (\Exception $e) {
            \Log::error('Error calculating payroll', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi tính lương: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($term_id)
    {
        $term = Term::findOrFail($term_id);
        $payrolls = Payroll::where('term_id', $term_id)
                           ->with(['teacher', 'assignment.courseClass.course'])
                           ->latest('total_amount')
                           ->paginate(20);
        
        $totalAmount = Payroll::where('term_id', $term_id)->sum('total_amount');

        return view('admin.payrolls.show', compact('payrolls', 'term', 'totalAmount'));
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($term_id)
    {
        Payroll::where('term_id', $term_id)->delete();

        return redirect()->route('admin.payrolls.index')
                         ->with('success', 'Đã xóa toàn bộ bảng lương của kì học đã chọn.');
    }
}
