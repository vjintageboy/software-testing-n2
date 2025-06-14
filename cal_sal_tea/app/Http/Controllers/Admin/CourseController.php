<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Faculty; // Thêm Faculty model
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager load quan hệ 'faculty' để tối ưu query
        $courses = Course::with('faculty')->paginate(10);
        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Lấy danh sách các khoa để hiển thị trong form
        $faculties = Faculty::all();
        return view('admin.courses.create', compact('faculties'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'course_code' => 'required|string|max:255|unique:courses,course_code',
            'credits' => 'required|integer|min:0',
            'faculty_id' => 'required|exists:faculties,id', // Validate faculty_id
        ]);

        Course::create($request->all());

        return redirect()->route('courses.index')->with('success', 'Học phần đã được tạo thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        return view('admin.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        // Lấy danh sách khoa cho form edit
        $faculties = Faculty::all();
        return view('admin.courses.edit', compact('course', 'faculties'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'course_code' => 'required|string|max:255|unique:courses,course_code,' . $course->id,
            'credits' => 'required|integer|min:0',
            'faculty_id' => 'required|exists:faculties,id', // Validate faculty_id
        ]);

        $course->update($request->all());

        return redirect()->route('courses.index')->with('success', 'Học phần đã được cập nhật thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('courses.index')->with('success', 'Học phần đã được xóa thành công.');
    }
}
