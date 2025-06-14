<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Faculty;
use App\Models\Degree;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Teacher::with(['faculty', 'degree']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('teacher_code', 'like', "%{$search}%");
            });
        }

        $teachers = $query->latest()->paginate(10);
        return view('admin.teachers.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $faculties = Faculty::all();
        $degrees = Degree::all();
        return view('admin.teachers.create', compact('faculties', 'degrees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'teacher_code' => 'required|string|max:50|unique:teachers',
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:teachers',
            'faculty_id' => 'required|exists:faculties,id',
            'degree_id' => 'required|exists:degrees,id',
        ]);

        Teacher::create($request->all());

        return redirect()->route('teachers.index')
                         ->with('success', 'Tạo mới giáo viên thành công.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher)
    {
        $faculties = Faculty::all();
        $degrees = Degree::all();
        return view('admin.teachers.edit', compact('teacher', 'faculties', 'degrees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'teacher_code' => 'required|string|max:50|unique:teachers,teacher_code,' . $teacher->id,
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:teachers,email,' . $teacher->id,
            'faculty_id' => 'required|exists:faculties,id',
            'degree_id' => 'required|exists:degrees,id',
            'is_active' => 'sometimes|boolean',
        ]);

        $teacher->update($request->all());

        return redirect()->route('teachers.index')
                         ->with('success', 'Cập nhật thông tin giáo viên thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher)
    {
        try {
            $teacher->delete();
            return redirect()->route('teachers.index')
                             ->with('success', 'Đã xóa giáo viên thành công.');
        } catch (\Exception $e) {
            return redirect()->route('teachers.index')
                             ->with('error', 'Không thể xóa giáo viên này. Vui lòng thử lại.');
        }
    }
}
