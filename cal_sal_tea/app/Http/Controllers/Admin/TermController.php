<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Term;
use Illuminate\Http\Request;

class TermController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Term::query();

        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('academic_year', 'like', "%{$search}%");
            });
        }

        $terms = $query->latest()->paginate(10);
        return view('admin.terms.index', compact('terms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.terms.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'academic_year' => 'required|string|max:20',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        Term::create($request->all());

        return redirect()->route('terms.index')
                         ->with('success', 'Tạo mới kì học thành công.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Term $term)
    {
        return view('admin.terms.edit', compact('term'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Term $term)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'academic_year' => 'required|string|max:20',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $term->update($request->all());

        return redirect()->route('terms.index')
                         ->with('success', 'Cập nhật kì học thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Term $term)
    {
        try {
            $term->delete();
            return redirect()->route('terms.index')
                             ->with('success', 'Đã xóa kì học thành công.');
        } catch (\Exception $e) {
            return redirect()->route('terms.index')
                             ->with('error', 'Không thể xóa kì học này. Vui lòng thử lại.');
        }
    }
}
