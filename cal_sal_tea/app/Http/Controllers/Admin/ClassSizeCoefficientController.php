<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSizeCoefficient;
use Illuminate\Http\Request;

class ClassSizeCoefficientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coefficients = ClassSizeCoefficient::orderBy('min_students')->paginate(10);
        return view('admin.class_size_coefficients.index', compact('coefficients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.class_size_coefficients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'min_students' => 'required|integer|min:0',
            'max_students' => 'required|integer|gte:min_students',
            'coefficient' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        ClassSizeCoefficient::create($request->all());

        return redirect()->route('class-size-coefficients.index')
                         ->with('success', 'Tạo mới hệ số thành công.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClassSizeCoefficient $classSizeCoefficient)
    {
        return view('admin.class_size_coefficients.edit', ['coefficient' => $classSizeCoefficient]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClassSizeCoefficient $classSizeCoefficient)
    {
        $request->validate([
            'min_students' => 'required|integer|min:0',
            'max_students' => 'required|integer|gte:min_students',
            'coefficient' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        $classSizeCoefficient->update($request->all());

        return redirect()->route('class-size-coefficients.index')
                         ->with('success', 'Cập nhật hệ số thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassSizeCoefficient $classSizeCoefficient)
    {
        $classSizeCoefficient->delete();
        return redirect()->route('class-size-coefficients.index')
                         ->with('success', 'Đã xóa hệ số thành công.');
    }
}