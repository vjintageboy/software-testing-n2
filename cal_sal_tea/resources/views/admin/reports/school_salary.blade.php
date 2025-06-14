@extends('adminlte::page')

@section('title', 'Báo cáo lương toàn trường')

@section('content_header')
    <h1>Báo cáo tiền dạy của giáo viên toàn trường</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.reports.school_salary') }}">
            @csrf
            <div class="row">
                <div class="col-md-10">
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

@if(isset($reportData))
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Kết quả báo cáo cho năm {{ $selectedYear }}</h3>
        </div>
        <div class="card-body">
            @if($reportData->count() > 0)
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên Khoa</th>
                        <th>Tổng lương năm</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($reportData as $faculty)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $faculty->name }}</td>
                            <td>{{ number_format($faculty->total_amount) }} VNĐ</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="2" class="text-right">Tổng cộng:</th>
                        <th>{{ number_format($totalSchoolSalary) }} VNĐ</th>
                    </tr>
                    </tfoot>
                </table>
            @else
                <p>Không có dữ liệu lương cho toàn trường trong năm {{ $selectedYear }}.</p>
            @endif
        </div>
    </div>
@endif
@stop 