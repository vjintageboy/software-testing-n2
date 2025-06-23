<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayrollParameter;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollParameterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // app/Http/Controllers/Admin/PayrollParameterController.php

    public function index()
    {
        // Đã sửa: Sắp xếp theo cột mới 'valid_from'
        $parameters = PayrollParameter::orderBy('valid_from', 'desc')->paginate(10);
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
            'valid_from' => 'required|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'description' => 'nullable|string|max:255',
        ]);

        $validFrom = Carbon::parse($request->input('valid_from'));
        $validTo = $request->input('valid_to') ? Carbon::parse($request->input('valid_to')) : null;

        // Tìm đơn giá có ngày hết hiệu lực gần nhất với ngày có hiệu lực của đơn giá mới
        $previousParam = PayrollParameter::where('valid_to', '<', $validFrom)
            ->orderBy('valid_to', 'desc')
            ->first();

        if ($previousParam) {
            // Kiểm tra xem có khoảng trống giữa đơn giá cũ và mới không
            if ($validFrom->diffInDays($previousParam->valid_to) > 1) {
                return back()->withErrors([
                    'valid_from' => 'Ngày có hiệu lực phải liền kề với ngày hết hiệu lực của đơn giá trước đó (' . $previousParam->valid_to->format('d/m/Y') . ')'
                ])->withInput();
            }
        }

        // Tìm đơn giá có ngày có hiệu lực sau ngày có hiệu lực của đơn giá mới
        $nextParam = PayrollParameter::where('valid_from', '>', $validFrom)
            ->orderBy('valid_from', 'asc')
            ->first();

        if ($nextParam) {
            // Nếu đơn giá mới có ngày hết hiệu lực
            if ($validTo) {
                // Kiểm tra xem có khoảng trống giữa đơn giá mới và đơn giá tiếp theo không
                if ($nextParam->valid_from->diffInDays($validTo) > 1) {
                    return back()->withErrors([
                        'valid_to' => 'Ngày hết hiệu lực phải liền kề với ngày có hiệu lực của đơn giá tiếp theo (' . $nextParam->valid_from->format('d/m/Y') . ')'
                    ])->withInput();
                }
            }
        }

        // Tạo đơn giá mới
        PayrollParameter::create([
            'base_pay_per_period' => $request->input('base_pay_per_period'),
            'valid_from' => $validFrom,
            'valid_to' => $validTo,
            'description' => $request->input('description'),
        ]);

        return redirect()->route('payroll-parameters.index')
                         ->with('success', 'Đã thêm mới và cập nhật lịch sử đơn giá thành công.');
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
            'valid_from' => 'required|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'description' => 'nullable|string|max:255',
        ]);

        // Chuyển đổi ngày tháng thành đối tượng Carbon
        $validFrom = Carbon::parse($request->input('valid_from'));
        $validTo = $request->input('valid_to') ? Carbon::parse($request->input('valid_to')) : null;

        // Tìm đơn giá có ngày hết hiệu lực gần nhất với ngày có hiệu lực của đơn giá đang sửa
        $previousParam = PayrollParameter::where('id', '!=', $payrollParameter->id)
            ->where('valid_to', '<', $validFrom)
            ->orderBy('valid_to', 'desc')
            ->first();

        if ($previousParam) {
            // Chuyển đổi ngày của đơn giá trước đó thành Carbon
            $previousValidTo = Carbon::parse($previousParam->valid_to);

            // Kiểm tra xem có khoảng trống giữa đơn giá cũ và đơn giá đang sửa không
            if ($validFrom->diffInDays($previousValidTo) > 1) {
                return back()->withErrors([
                    'valid_from' => 'Ngày có hiệu lực phải liền kề với ngày hết hiệu lực của đơn giá trước đó (' . $previousValidTo->format('d/m/Y') . ')'
                ])->withInput();
            }
        }

        // Tìm đơn giá có ngày có hiệu lực sau ngày có hiệu lực của đơn giá đang sửa
        $nextParam = PayrollParameter::where('id', '!=', $payrollParameter->id)
            ->where('valid_from', '>', $validFrom)
            ->orderBy('valid_from', 'asc')
            ->first();

        if ($nextParam) {
            // Chuyển đổi ngày của đơn giá tiếp theo thành Carbon
            $nextValidFrom = Carbon::parse($nextParam->valid_from);

            // Nếu đơn giá đang sửa có ngày hết hiệu lực
            if ($validTo) {
                // Kiểm tra xem có khoảng trống giữa đơn giá đang sửa và đơn giá tiếp theo không
                if ($nextValidFrom->diffInDays($validTo) > 1) {
                    return back()->withErrors([
                        'valid_to' => 'Ngày hết hiệu lực phải liền kề với ngày có hiệu lực của đơn giá tiếp theo (' . $nextValidFrom->format('d/m/Y') . ')'
                    ])->withInput();
                }
            }
        }

        $payrollParameter->update([
            'base_pay_per_period' => $request->input('base_pay_per_period'),
            'valid_from' => $validFrom,
            'valid_to' => $validTo,
            'description' => $request->input('description'),
        ]);

        return redirect()->route('payroll-parameters.index')
                         ->with('success', 'Cập nhật đơn giá thành công.');
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
