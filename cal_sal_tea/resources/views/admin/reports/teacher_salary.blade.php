@extends('adminlte::page')

@section('title', 'Báo cáo lương giáo viên')

@section('content_header')
    <h1>Báo cáo tiền dạy của giáo viên theo năm</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.reports.teacher_salary') }}">
                @csrf
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="teacher_id">Chọn giáo viên:</label>
                            <select name="teacher_id" id="teacher_id" class="form-control">
                                <option value="">-- Tất cả giáo viên --</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ $selectedTeacherId == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->full_name }} ({{ $teacher->teacher_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="year">Chọn năm:</label>
                            <select name="year" id="year" class="form-control">
                                <option value="">-- Chọn năm --</option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                        Năm {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary form-control">Xem báo cáo</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(isset($payrolls))
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Kết quả báo cáo cho năm {{ $selectedYear }}</h3>
            </div>
            <div class="card-body">
                @if($payrolls->count() > 0)
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>STT</th>
                            <th>Học kỳ</th>
                            <th>Tổng lương</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($payrolls as $payroll)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $payroll->term->name }}</td>
                                <td>{{ number_format($payroll->total_amount) }} VNĐ</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="2" class="text-right">Tổng cộng:</th>
                            <th>{{ number_format($totalSalary) }} VNĐ</th>
                        </tr>
                        </tfoot>
                    </table>
                @else
                    <p>Không có dữ liệu lương cho giáo viên này trong năm {{ $selectedYear }}.</p>
                @endif
            </div>
        </div>
    @endif
@stop 