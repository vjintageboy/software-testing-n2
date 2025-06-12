<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Teacher;
use App\Models\CourseClass;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assignments = Assignment::with(['teacher', 'courseClass.course', 'courseClass.term'])->latest()->paginate(15);
        return view('admin.assignments.index', compact('assignments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $teachers = Teacher::where('is_active', true)->orderBy('full_name')->get();
        
        // Lấy danh sách các lớp CHƯA được phân công
        $unassignedClasses = CourseClass::whereDoesntHave('assignment')->with(['course', 'term'])->get();
        
        return view('admin.assignments.create', compact('teachers', 'unassignedClasses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'course_class_id' => 'required|exists:course_classes,id|unique:assignments,course_class_id',
            'notes' => 'nullable|string',
        ]);

        Assignment::create($request->all());

        return redirect()->route('assignments.index')
                         ->with('success', 'Phân công giảng dạy thành công.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Assignment $assignment)
    {
        $teachers = Teacher::where('is_active', true)->orderBy('full_name')->get();

        // Lấy danh sách các lớp CHƯA được phân công, CỘNG VỚI lớp hiện tại của assignment này
        $unassignedClasses = CourseClass::whereDoesntHave('assignment')
                                        ->orWhere('id', $assignment->course_class_id)
                                        ->with(['course', 'term'])->get();

        return view('admin.assignments.edit', compact('assignment', 'teachers', 'unassignedClasses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Assignment $assignment)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'course_class_id' => 'required|exists:course_classes,id|unique:assignments,course_class_id,' . $assignment->id,
            'notes' => 'nullable|string',
        ]);

        $assignment->update($request->all());

        return redirect()->route('assignments.index')
                         ->with('success', 'Cập nhật phân công thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assignment $assignment)
    {
        $assignment->delete();
        return redirect()->route('assignments.index')
                         ->with('success', 'Đã hủy phân công thành công.');
    }
}
