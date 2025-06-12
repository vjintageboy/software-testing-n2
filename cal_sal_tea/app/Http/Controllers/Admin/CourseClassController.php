<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseClass;
use App\Models\Course;
use App\Models\Term;
use Illuminate\Http\Request;

class CourseClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classes = CourseClass::with(['course', 'term'])->latest()->paginate(10);
        return view('admin.course_classes.index', compact('classes'));
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
     * Show the form for editing the specified resource.
     */
    public function edit(CourseClass $courseClass)
    {
        $courses = Course::orderBy('name')->get();
        $terms = Term::latest()->get();
        return view('admin.course_classes.edit', [
            'class' => $courseClass,
            'courses' => $courses,
            'terms' => $terms
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CourseClass $courseClass)
    {
        $request->validate([
            'class_code' => 'required|string|max:50|unique:course_classes,class_code,' . $courseClass->id,
            'course_id' => 'required|exists:courses,id',
            'term_id' => 'required|exists:terms,id',
            'number_of_students' => 'required|integer|min:0',
        ]);

        $courseClass->update($request->all());

        return redirect()->route('classes.index')
                         ->with('success', 'Cập nhật lớp học phần thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseClass $courseClass)
    {
        try {
            if ($courseClass->assignments()->exists()) {
                 return redirect()->route('classes.index')
                             ->with('error', 'Không thể xóa lớp này vì đã được phân công giảng dạy.');
            }

            $courseClass->delete();
            return redirect()->route('classes.index')
                             ->with('success', 'Đã xóa lớp học phần thành công.');
        } catch (\Exception $e) {
            return redirect()->route('classes.index')
                             ->with('error', 'Không thể xóa lớp học phần này. Vui lòng thử lại.');
        }
    }
}
