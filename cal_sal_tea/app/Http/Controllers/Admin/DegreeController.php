<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Degree;
use Illuminate\Http\Request;

class DegreeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Degree::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('abbreviation', 'like', "%{$search}%");
        }

        $degrees = $query->latest()->paginate(10);
        return view('admin.degrees.index', compact('degrees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.degrees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Cập nhật validation rules và custom messages
        $request->validate(
            [
                'name' => [
                    'required', 'string', 'max:255', 'unique:degrees',
                    // Sửa lại: Regex cho phép chữ, số, và ký tự tiếng Việt
                    'regex:/^[a-zA-Z0-9\s\p{L}]+$/u'
                ],
                'abbreviation' => 'required|string|max:20|unique:degrees',
                'coefficient' => 'required|numeric|min:0',
            ],
            [
                'name.required' => 'Tên đầy đủ không được để trống.',
                'name.unique' => 'Tên đầy đủ này đã tồn tại.',
                'name.regex' => 'Tên đầy đủ chỉ được chứa chữ cái, số và khoảng trắng.',
                'abbreviation.required' => 'Tên viết tắt không được để trống.',
                'abbreviation.unique' => 'Tên viết tắt này đã tồn tại.',
                'abbreviation.max' => 'Tên viết tắt không được vượt quá 20 ký tự.',
            ]
        );

        Degree::create($request->all());

        return redirect()->route('degrees.index')
                         ->with('success', 'Tạo mới bằng cấp thành công.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Degree $degree)
    {
        return view('admin.degrees.edit', compact('degree'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Degree $degree)
    {
        // Cập nhật validation rules và custom messages
        $request->validate(
            [
                'name' => [
                    'required', 'string', 'max:255', 'unique:degrees,name,' . $degree->id,
                    // Sửa lại: Regex cho phép chữ, số, và ký tự tiếng Việt
                    'regex:/^[a-zA-Z0-9\s\p{L}]+$/u'
                ],
                'abbreviation' => 'required|string|max:20|unique:degrees,abbreviation,' . $degree->id,
                'coefficient' => 'required|numeric|min:0',
            ],
            [
                'name.required' => 'Tên đầy đủ không được để trống.',
                'name.unique' => 'Tên đầy đủ này đã tồn tại.',
                'name.regex' => 'Tên đầy đủ chỉ được chứa chữ cái, số và khoảng trắng.',
                'abbreviation.required' => 'Tên viết tắt không được để trống.',
                'abbreviation.unique' => 'Tên viết tắt này đã tồn tại.',
                'abbreviation.max' => 'Tên viết tắt không được vượt quá 20 ký tự.',
            ]
        );

        $degree->update($request->all());

        return redirect()->route('degrees.index')
                         ->with('success', 'Cập nhật bằng cấp thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Degree $degree)
    {
        try {
            $degree->delete();
            return redirect()->route('degrees.index')
                             ->with('success', 'Đã xóa bằng cấp thành công.');
        } catch (\Exception $e) {
            return redirect()->route('degrees.index')
                             ->with('error', 'Không thể xóa bằng cấp này. Vui lòng thử lại.');
        }
    }
}
