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
            \Log::info('Starting payroll calculation', ['request' => $request->all()]);

            $request->validate(['term_id' => 'required|exists:terms,id']);

            $term = Term::findOrFail($request->term_id);
            \Log::info('Found term', ['term' => $term->toArray()]);

            $referenceDate = $term->start_date;

            $assignments = Assignment::whereHas('courseClass', function($query) use ($term) {
                $query->where('term_id', $term->id);
            })->with(['teacher.degree', 'courseClass.course'])->get();

            if ($assignments->isEmpty()) {
                \Log::warning('No assignments found for term', ['term_id' => $term->id]);
                return redirect()->back()->with('error', 'Kì học này không có phân công nào để tính lương.');
            }
            \Log::info('Found assignments', ['count' => $assignments->count()]);

            $basePayParam = PayrollParameter::where('valid_from', '<=', $referenceDate)
                ->where(function ($query) use ($referenceDate) {
                    $query->where('valid_to', '>=', $referenceDate)
                          ->orWhereNull('valid_to');
                })
                ->first();

            if (!$basePayParam) {
                \Log::warning('No base pay parameter found', ['reference_date' => $referenceDate]);
                return redirect()->back()->with('error', 'Chưa có đơn giá nào được áp dụng cho kì học này. Vui lòng thêm trong mục "Đơn giá Tiết dạy".');
            }
            \Log::info('Found base pay parameter', ['base_pay_param' => $basePayParam->toArray()]);
            $basePayPerPeriod = $basePayParam->base_pay_per_period;

            // 1. Lấy TẤT CẢ các quy tắc hợp lệ bằng ->get() và đổi tên biến cho rõ nghĩa
            $classSizeRules = ClassSizeCoefficient::where('valid_from', '<=', $referenceDate)
                ->where(function ($query) use ($referenceDate) {
                    $query->where('valid_to', '>=', $referenceDate)
                          ->orWhereNull('valid_to');
                })
                ->get();

            if ($classSizeRules->isEmpty()) {
                \Log::warning('No class size coefficient rules found', ['reference_date' => $referenceDate]);
                return redirect()->back()->with('error', 'Chưa có hệ số quy mô lớp học nào được áp dụng cho kì học này. Vui lòng thêm trong mục "Hệ số Quy mô Lớp học".');
            }
            \Log::info('Found class size rules', ['count' => $classSizeRules->count()]);

            // 2. Truyền đúng biến $classSizeRules vào transaction
            DB::transaction(function () use ($assignments, $term, $basePayPerPeriod, $classSizeRules) {
                Payroll::where('term_id', $term->id)->delete();

                foreach ($assignments as $assignment) {
                    $teacherCoefficient = $assignment->teacher->degree->coefficient;
                    $standardPeriods = $assignment->courseClass->course->standard_periods;
                    $courseCoefficient = $assignment->courseClass->course->coefficient;
                    $studentCount = $assignment->courseClass->number_of_students;

                    // 3. Sửa vòng lặp để duyệt qua danh sách $classSizeRules
                    $classCoefficient = 0.0; // Mặc định là 0
                    foreach ($classSizeRules as $rule) {
                        if ($studentCount >= $rule->min_students && $studentCount <= $rule->max_students) {
                            $classCoefficient = $rule->coefficient;
                            break;
                        }
                    }

                    $convertedPeriods = $standardPeriods * ($courseCoefficient + $classCoefficient);
                    $totalAmount = $convertedPeriods * $teacherCoefficient * $basePayPerPeriod;

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

            // === SỬA LỖI ROUTE NAME ===
            // Đổi tên route về lại 'payrolls.index' để khớp với cấu hình dự án của bạn
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

        return redirect()->route('payrolls.index')
                         ->with('success', 'Đã xóa toàn bộ bảng lương của kì học đã chọn.');
    }
}
