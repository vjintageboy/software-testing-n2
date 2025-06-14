@extends('adminlte::page')

@section('title', 'Báo cáo lương theo khoa')

@section('content_header')
    <h1>Báo cáo tiền dạy của giáo viên theo khoa</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.reports.faculty_salary') }}">
            @csrf
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="faculty_id">Chọn khoa:</label>
                        <select name="faculty_id" id="faculty_id" class="form-control">
                            <option value="">-- Chọn khoa --</option>
                            @foreach($faculties as $faculty)
                                <option value="{{ $faculty->id }}" {{ $selectedFacultyId == $faculty->id ? 'selected' : '' }}>
                                    {{ $faculty->name }}
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
                        <th>Tên Giáo Viên</th>
                        <th>Mã Giáo Viên</th>
                        <th>Tổng lương năm</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($reportData as $teacher)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $teacher->full_name }}</td>
                            <td>{{ $teacher->teacher_code }}</td>
                            <td>{{ number_format($teacher->total_amount) }} VNĐ</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="3" class="text-right">Tổng cộng:</th>
                        <th>{{ number_format($totalFacultySalary) }} VNĐ</th>
                    </tr>
                    </tfoot>
                </table>
            @else
                <p>Không có dữ liệu lương cho khoa này trong năm {{ $selectedYear }}.</p>
            @endif
        </div>
    </div>
@endif
@stop 