// ... existing code ...
public function index()
{
    // Đã sửa: Sắp xếp theo cột mới 'effective_date'
    $parameters = PayrollParameter::orderBy('effective_date', 'desc')->paginate(10);
    return view('admin.payroll_parameters.index', compact('parameters'));
}

    /**
// ... existing code ...
