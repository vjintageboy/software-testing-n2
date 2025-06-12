<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayrollParameter;
use Illuminate\Http\Request;

class PayrollParameterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $parameters = PayrollParameter::orderBy('effective_date', 'desc')->paginate(10);
        return view('admin.payroll_parameters.index', compact('parameters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.payroll_parameters.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'base_pay_per_period' => 'required|numeric|min:0',
            'effective_date' => 'required|date|unique:payroll_parameters,effective_date',
            'description' => 'nullable|string|max:255',
        ]);

        PayrollParameter::create($request->all());

        return redirect()->route('payroll-parameters.index')
                         ->with('success', 'Tạo mới tham số thành công.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PayrollParameter $payrollParameter)
    {
        return view('admin.payroll_parameters.edit', ['parameter' => $payrollParameter]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PayrollParameter $payrollParameter)
    {
        $request->validate([
            'base_pay_per_period' => 'required|numeric|min:0',
            'effective_date' => 'required|date|unique:payroll_parameters,effective_date,' . $payrollParameter->id,
            'description' => 'nullable|string|max:255',
        ]);

        $payrollParameter->update($request->all());

        return redirect()->route('payroll-parameters.index')
                         ->with('success', 'Cập nhật tham số thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PayrollParameter $payrollParameter)
    {
        $payrollParameter->delete();
        return redirect()->route('payroll-parameters.index')
                         ->with('success', 'Đã xóa tham số thành công.');
    }
}
