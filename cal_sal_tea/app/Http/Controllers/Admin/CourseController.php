<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::latest()->paginate(10);
        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.courses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_code' => 'required|string|max:50|unique:courses',
            'name' => 'required|string|max:255',
            'credits' => 'required|integer|min:0',
            'standard_periods' => 'required|integer|min:1',
            'coefficient' => 'required|numeric|min:0',
        ]);

        Course::create($request->all());

        return redirect()->route('courses.index')
                         ->with('success', 'Tạo mới học phần thành công.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        return view('admin.courses.edit', compact('course'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        $request->validate([
            'course_code' => 'required|string|max:50|unique:courses,course_code,' . $course->id,
            'name' => 'required|string|max:255',
            'credits' => 'required|integer|min:0',
            'standard_periods' => 'required|integer|min:1',
            'coefficient' => 'required|numeric|min:0',
        ]);

        $course->update($request->all());

        return redirect()->route('courses.index')
                         ->with('success', 'Cập nhật học phần thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        try {
            $course->delete();
            return redirect()->route('courses.index')
                             ->with('success', 'Đã xóa học phần thành công.');
        } catch (\Exception $e) {
            return redirect()->route('courses.index')
                             ->with('error', 'Không thể xóa học phần này. Vui lòng thử lại.');
        }
    }
}
