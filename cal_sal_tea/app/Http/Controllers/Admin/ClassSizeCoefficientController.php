<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSizeCoefficient;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
            'min_students' => 'required|integer',
            'max_students' => 'nullable|integer',
            'coefficient' => 'required|numeric',
            'valid_from' => 'required|date',
        ]);

        $validFrom = Carbon::parse($request->input('valid_from'));

        // Tương tự, tìm và chốt hệ số cũ đang có hiệu lực
        // (Logic này có thể cần điều chỉnh nếu bạn muốn quản lý các khoảng min/max phức tạp hơn,
        // nhưng đây là logic cơ bản để đóng các quy tắc cũ)
        $currentActiveCoefficients = ClassSizeCoefficient::whereNull('valid_to')->get();
        foreach ($currentActiveCoefficients as $currentActiveCoefficient) {
            $endDateForOldParam = $validFrom->copy()->subDay();
            $currentActiveCoefficient->update(['valid_to' => $endDateForOldParam]);
        }


        // Tạo hệ số mới
        ClassSizeCoefficient::create([
            'min_students' => $request->input('min_students'),
            'max_students' => $request->input('max_students'),
            'coefficient' => $request->input('coefficient'),
            'valid_from' => $validFrom,
            'valid_to' => null,
        ]);

        return redirect()->route('admin.class_size_coefficients.index')
                         ->with('success', 'Đã thêm mới và cập nhật lịch sử hệ số thành công.');
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
