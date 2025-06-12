<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $faculties = Faculty::latest()->paginate(10);
        return view('admin.faculties.index', compact('faculties'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.faculties.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'required|string|max:20|unique:faculties',
            'description' => 'nullable|string',
        ]);

        Faculty::create($request->all());

        return redirect()->route('faculties.index')
                         ->with('success', 'Tạo mới khoa thành công.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Faculty $faculty)
    {
        return view('admin.faculties.edit', compact('faculty'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Faculty $faculty)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'required|string|max:20|unique:faculties,abbreviation,' . $faculty->id,
            'description' => 'nullable|string',
        ]);

        $faculty->update($request->all());

        return redirect()->route('faculties.index')
                         ->with('success', 'Cập nhật khoa thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faculty $faculty)
    {
        try {
            $faculty->delete();
            return redirect()->route('faculties.index')
                             ->with('success', 'Đã xóa khoa thành công.');
        } catch (\Exception $e) {
            return redirect()->route('faculties.index')
                             ->with('error', 'Không thể xóa khoa này. Vui lòng thử lại.');
        }
    }
}
