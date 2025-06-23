<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseClass;
use App\Models\Course;
use App\Models\Term;
use Illuminate\Http\Request;
use App\Models\ClassSizeCoefficient;
use App\Models\Teacher; // <-- Thêm model Teacher
use App\Models\Assignment;

// <-- Thêm model Assignment


class CourseClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CourseClass::with(['course', 'term', 'assignment.teacher']);

        // Thêm điều kiện tìm kiếm nếu có
        if ($request->has('search') && $request->search !== '') {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('class_code', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('course', function ($qCourse) use ($searchTerm) {
                      $qCourse->where('name', 'like', '%' . $searchTerm . '%');
                  })
                  ->orWhereHas('assignment.teacher', function ($qTeacher) use ($searchTerm) {
                      $qTeacher->where('full_name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        // Thêm điều kiện lọc theo kỳ học
        if ($request->has('term_id') && $request->term_id !== '' && $request->term_id !== null) {
            $query->where('term_id', $request->term_id);
        }

        $classes = $query->latest()->paginate(15)->appends($request->query());
        $terms = Term::orderBy('academic_year', 'desc')->orderBy('name')->get();

        return view('admin.course_classes.index', compact('classes', 'terms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $courses = Course::orderBy('name')->get();
        $terms = Term::latest()->get();
        return view('admin.course_classes.create', compact('courses', 'terms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_code' => 'required|string|max:50|unique:course_classes',
            'course_id' => 'required|exists:courses,id',
            'term_id' => 'required|exists:terms,id',
            'number_of_students' => 'required|integer|min:0',
        ]);

        CourseClass::create($request->all());

        return redirect()->route('classes.index')
                         ->with('success', 'Tạo mới lớp học phần thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseClass $class)
    {
        return redirect()->route('classes.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseClass $class)
    {
        $courses = Course::orderBy('name')->get();
        $terms = Term::latest()->get();
        return view('admin.course_classes.edit', [
            'class' => $class,
            'courses' => $courses,
            'terms' => $terms
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CourseClass $class)
    {
        $request->validate([
            'class_code' => 'required|string|max:50|unique:course_classes,class_code,' . $class->id,
            'course_id' => 'required|exists:courses,id',
            'term_id' => 'required|exists:terms,id',
            'number_of_students' => 'required|integer|min:0',
        ]);

        $class->update($request->all());

        return redirect()->route('classes.index')
                         ->with('success', 'Cập nhật lớp học phần thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseClass $class)
    {
        \Log::info('Bắt đầu xoá lớp học phần', ['id' => $class->id]);
        try {
            $hasAssignment = $class->assignment()->exists();
            \Log::info('Kiểm tra assignment', ['id' => $class->id, 'has_assignment' => $hasAssignment]);
            if ($hasAssignment) {
                 \Log::warning('Không thể xoá lớp đã phân công', ['id' => $class->id]);
                 return redirect()->route('classes.index')
                             ->with('error', 'Không thể xóa lớp này vì đã được phân công giảng dạy.');
            }

            $deleted = $class->delete();
            \Log::info('Kết quả xoá lớp học phần', ['id' => $class->id, 'deleted' => $deleted]);
            return redirect()->route('classes.index')
                             ->with('success', 'Đã xóa lớp học phần thành công.');
        } catch (\Exception $e) {
            \Log::error('Lỗi khi xoá lớp học phần', ['id' => $class->id, 'error' => $e->getMessage()]);
            return redirect()->route('classes.index')
                             ->with('error', 'Không thể xóa lớp học phần này. Vui lòng thử lại.');
        }
    }
    /**
     * Hiển thị form để tạo nhanh nhiều lớp học phần.
     */
    public function createBulk()
    {
        $courses = Course::all();
        $terms = Term::all();
        return view('admin.course_classes.create_bulk', compact('courses', 'terms'));
    }

    /**
     * Lưu trữ nhiều lớp học phần được tạo từ form.
     */
    public function storeBulk(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'term_id' => 'required|exists:terms,id',
            'number_of_students' => 'required|integer|min:0',
            'number_of_classes' => 'required|integer|min:1|max:100', // Giới hạn tạo tối đa 100 lớp 1 lần
        ]);

        $course = Course::findOrFail($request->course_id);
        $term = Term::findOrFail($request->term_id);
        $numberOfClasses = $request->number_of_classes;

        // Bắt đầu vòng lặp để tạo các lớp
        for ($i = 1; $i <= $numberOfClasses; $i++) {
            // Đệm số 0 vào trước các số từ 1-9 (VD: 01, 02)
            $classIndex = str_pad($i, 2, '0', STR_PAD_LEFT);

            CourseClass::create([
                'course_id' => $course->id,
                'term_id' => $term->id,
                // Tạo mã lớp theo quy tắc: MÃ_HP-MÃ_HK-SỐ_THỨ_TỰ
                'class_code' => "{$course->course_code}-{$term->term_code}-{$classIndex}",
                'number_of_students' => $request->number_of_students,
            ]);
        }

        return redirect()->route('classes.index')
                         ->with('success', "Đã tạo thành công {$numberOfClasses} lớp học phần cho môn {$course->name}.");
    }

    /**
     * Remove multiple course classes from storage.
     */
    public function bulkDelete(Request $request)
    {
        \Log::info('Bulk delete request received', ['data' => $request->all()]);
        
        $request->validate([
            'selected_classes' => 'required|array|min:1',
            'selected_classes.*' => 'exists:course_classes,id',
        ], [
            'selected_classes.required' => 'Vui lòng chọn ít nhất một lớp để xóa.',
            'selected_classes.min' => 'Vui lòng chọn ít nhất một lớp để xóa.',
            'selected_classes.*.exists' => 'Một trong các lớp được chọn không hợp lệ.',
        ]);

        $selectedClasses = CourseClass::whereIn('id', $request->selected_classes)->get();
        \Log::info('Bắt đầu bulk delete', ['ids' => $selectedClasses->pluck('id')->toArray()]);

        if ($selectedClasses->isEmpty()) {
            \Log::warning('Không có lớp nào được chọn để xóa');
            return redirect()->route('classes.index')
                ->with('error', 'Không có lớp nào được chọn để xóa.');
        }

        // Check if any of the selected classes have assignments
        $classesWithAssignments = $selectedClasses->filter(function ($class) {
            $hasAssignment = $class->assignment()->exists();
            \Log::info('Kiểm tra assignment trong bulk', ['id' => $class->id, 'has_assignment' => $hasAssignment]);
            return $hasAssignment;
        })->count();
        if ($classesWithAssignments > 0) {
            \Log::warning('Có lớp đã phân công, không xoá bulk', ['count' => $classesWithAssignments]);
            return redirect()->route('classes.index')
                ->with('error', 'Không thể xóa các lớp đã được phân công giảng dạy.');
        }

        $deletedCount = 0;
        $failedCount = 0;
        foreach ($selectedClasses as $class) {
            try {
                $deleted = $class->delete();
                \Log::info('Kết quả xoá lớp trong bulk', ['id' => $class->id, 'deleted' => $deleted]);
                if ($deleted) {
                    $deletedCount++;
                } else {
                    $failedCount++;
                }
            } catch (\Exception $e) {
                $failedCount++;
                \Log::error('Lỗi khi xoá lớp trong bulk', ['id' => $class->id, 'error' => $e->getMessage()]);
            }
        }
        $message = "Đã xóa thành công {$deletedCount} lớp học phần.";
        if ($failedCount > 0) {
            $message .= " Không thể xóa {$failedCount} lớp do lỗi.";
        }
        return redirect()->route('classes.index')
            ->with('success', $message);
    }
}
