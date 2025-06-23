<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\CourseClass;
use App\Models\Teacher;
use App\Models\Term;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    /**
     * Hiển thị danh sách phân công đã thực hiện.
     */
    public function index(Request $request)
    {
        $query = Assignment::with(['teacher', 'courseClass.course', 'courseClass.term']);

        // Thêm điều kiện tìm kiếm nếu có
        if ($request->has('search') && $request->search !== '') {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('teacher', function ($qTeacher) use ($searchTerm) {
                    $qTeacher->where('full_name', 'like', '%' . $searchTerm . '%');
                })
                ->orWhereHas('courseClass.course', function ($qCourse) use ($searchTerm) {
                    $qCourse->where('name', 'like', '%' . $searchTerm . '%');
                });
            });
        }

        // Thêm điều kiện lọc theo kỳ học
        if ($request->has('term_id') && $request->term_id !== '' && $request->term_id !== null) {
            $query->whereHas('courseClass', function ($q) use ($request) {
                $q->where('term_id', $request->term_id);
            });
        }

        $assignments = $query->latest()->paginate(15)->appends($request->query());
        $terms = Term::orderBy('academic_year', 'desc')->orderBy('name')->get();

        return view('admin.assignments.index', compact('assignments', 'terms'));
    }

    /**
     * Hiển thị form tạo phân công mới, chỉ hiển thị lớp chưa phân công.
     */
    public function create(Request $request)
    {
        // SỬA LẠI: Sắp xếp theo cột 'full_name' cho đúng với CSDL
        $teachers = Teacher::orderBy('full_name')->get();
        $terms = Term::orderBy('start_date', 'desc')->get();

        // Lấy các lớp chưa được phân công (không có assignment)
        $query = CourseClass::whereDoesntHave('assignment')
                            ->with(['course', 'term']);

        // Nếu có từ khóa tìm kiếm
        if ($request->has('search') && $request->search !== '') {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('class_code', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('course', function ($qCourse) use ($searchTerm) {
                      $qCourse->where('name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        // Thêm điều kiện lọc theo kỳ học
        if ($request->has('term_id') && $request->term_id !== '' && $request->term_id !== null) {
            $query->where('term_id', $request->term_id);
        }

        // Phân trang
        $unassignedClasses = $query->latest()->paginate(10)->appends($request->query());

        return view('admin.assignments.create', compact('teachers', 'unassignedClasses', 'terms'));
    }

    /**
     * Lưu phân công giảng viên cho nhiều lớp học phần.
     */
    public function store(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'course_class_ids' => 'required|array|min:1',
            'course_class_ids.*' => 'exists:course_classes,id',
        ], [
            'teacher_id.required' => 'Vui lòng chọn một giảng viên.',
            'course_class_ids.required' => 'Vui lòng chọn ít nhất một lớp học phần để phân công.',
        ]);

        $teacherId = $request->teacher_id;
        $classIds = $request->course_class_ids;

        foreach ($classIds as $classId) {
            Assignment::updateOrCreate(
                ['course_class_id' => $classId],
                ['teacher_id' => $teacherId]
            );
        }

        // Sửa lại để bỏ tiền tố 'admin.'
        return redirect()->route('assignments.index')
                         ->with('success', 'Đã phân công thành công cho ' . count($classIds) . ' lớp học phần.');
    }

    /**
     * Trang chi tiết (chưa dùng).
     */
    public function show(Assignment $assignment)
    {
        // Nếu cần, có thể return view('admin.assignments.show', ...)
    }

    /**
     * Hiển thị form sửa phân công.
     */
    public function edit(Assignment $assignment)
    {
        // SỬA LẠI: Sắp xếp theo cột 'full_name' cho đúng với CSDL
        $teachers = Teacher::orderBy('full_name')->get();

        return view('admin.assignments.edit', compact('assignment', 'teachers'));
    }

    /**
     * Cập nhật thông tin phân công.
     */
    public function update(Request $request, Assignment $assignment)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'notes' => 'nullable|string', // Thêm validation cho notes
        ]);

        $assignment->update([
            'teacher_id' => $request->teacher_id,
            'notes' => $request->notes, // Thêm notes vào mảng cập nhật
        ]);

        // Sửa lại để bỏ tiền tố 'admin.'
        return redirect()->route('assignments.index')->with('success', 'Cập nhật phân công thành công.');
    }

    /**
     * Xóa phân công.
     */
    public function destroy(Assignment $assignment)
    {
        $assignment->delete();

        // Sửa lại để bỏ tiền tố 'admin.'
        return redirect()->route('assignments.index')->with('success', 'Đã xóa phân công thành công.');
    }

    /**
     * Xóa nhiều phân công đã chọn.
     */
    public function destroyBulk(Request $request)
    {
        $request->validate([
            'assignment_ids' => 'required|array|min:1',
            'assignment_ids.*' => 'exists:assignments,id',
            'confirmation_text' => 'required|in:XÁC NHẬN',
        ], [
            'assignment_ids.required' => 'Vui lòng chọn ít nhất một phân công để xóa.',
            'assignment_ids.*.exists' => 'Một trong các phân công được chọn không hợp lệ.',
            'confirmation_text.required' => 'Vui lòng nhập "XÁC NHẬN" để xóa.',
            'confirmation_text.in' => 'Chuỗi xác nhận không đúng. Vui lòng nhập "XÁC NHẬN".',
        ]);

        $assignmentIds = $request->assignment_ids;

        DB::transaction(function () use ($assignmentIds) {
            Assignment::whereIn('id', $assignmentIds)->delete();
        });

        return redirect()->route('assignments.index')
                         ->with('success', 'Đã xóa thành công ' . count($assignmentIds) . ' phân công.');
    }
}
