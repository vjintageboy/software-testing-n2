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

class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Group payrolls by term to show a summary
        $payrollSummaries = Payroll::select('term_id', DB::raw('count(*) as total_records, sum(total_amount) as total_sum, max(calculation_date) as last_calculated'))
            ->with('term')
            ->groupBy('term_id')
            ->orderBy('last_calculated', 'desc')
            ->paginate(10);
            
        return view('admin.payrolls.index', compact('payrollSummaries'));
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
        $request->validate(['term_id' => 'required|exists:terms,id']);
        $termId = $request->term_id;
        $term = Term::findOrFail($termId);

        // Lấy tất cả các phân công trong kì học này
        $assignments = Assignment::whereHas('courseClass', function($query) use ($termId) {
            $query->where('term_id', $termId);
        })->with(['teacher.degree', 'courseClass.course'])->get();

        if ($assignments->isEmpty()) {
            return redirect()->back()->with('error', 'Kì học này không có phân công nào để tính lương.');
        }

        // Lấy các tham số tính lương
        $basePay = PayrollParameter::where('effective_date', '<=', $term->start_date)->orderBy('effective_date', 'desc')->first();
        if (!$basePay) {
            return redirect()->back()->with('error', 'Chưa có đơn giá tiết dạy nào được áp dụng cho kì học này. Vui lòng thêm trong mục "Đơn giá Tiết dạy".');
        }
        $classSizeCoefficients = ClassSizeCoefficient::all();

        DB::transaction(function () use ($assignments, $term, $basePay, $classSizeCoefficients) {
            // Xóa các bảng lương cũ của kì này để tính lại
            Payroll::where('term_id', $term->id)->delete();

            foreach ($assignments as $assignment) {
                // Lấy các giá trị cần thiết
                $teacherCoefficient = $assignment->teacher->degree->coefficient; // hệ_số_giáo_viên
                $basePayPerPeriod = $basePay->base_pay_per_period; // tiền_dạy_một_tiết
                $standardPeriods = $assignment->courseClass->course->standard_periods; // Số tiết thực tế
                $courseCoefficient = $assignment->courseClass->course->coefficient; // hệ_số_học_phần
                
                // Tìm hệ số sĩ số (hệ_số_lớp)
                $studentCount = $assignment->courseClass->number_of_students;
                $classCoeffRule = $classSizeCoefficients
                    ->where('min_students', '<=', $studentCount)
                    ->where('max_students', '>=', $studentCount)
                    ->first();
                $classCoefficient = $classCoeffRule ? $classCoeffRule->coefficient : 0.0; // Nếu không có quy tắc, hệ số lớp là 0

                // =============================================================
                // === BẮT ĐẦU CẬP NHẬT CÔNG THỨC TÍNH LƯƠNG THEO YÊU CẦU MỚI ===
                // =============================================================

                // 1. Tính số tiết quy đổi
                $convertedPeriods = $standardPeriods * ($courseCoefficient + $classCoefficient);

                // 2. Tính thành tiền cuối cùng
                $totalAmount = $convertedPeriods * $teacherCoefficient * $basePayPerPeriod;

                // =============================================================
                // === KẾT THÚC CẬP NHẬT CÔNG THỨC TÍNH LƯƠNG ===
                // =============================================================

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

        return redirect()->route('payrolls.index')->with('success', 'Đã tính toán và lưu bảng lương cho ' . $term->name . ' - ' . $term->academic_year . ' thành công!');
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

        return redirect()->route('payrolls.index')
                         ->with('success', 'Đã xóa toàn bộ bảng lương của kì học đã chọn.');
    }
}
