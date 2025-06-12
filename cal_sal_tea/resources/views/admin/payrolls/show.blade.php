@extends('adminlte::page')

@section('title', 'Chi tiết Bảng lương')

@section('content_header')
    <h1 class="m-0 text-dark">Chi tiết Bảng lương: {{ $term->name }} - {{ $term->academic_year }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tổng cộng: <b>{{ number_format($totalAmount, 0, ',', '.') }} VNĐ</b></h3>
                    <div class="card-tools">
                        <a href="{{ route('payrolls.index') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-arrow-left"></i> Quay lại Lịch sử
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Giáo viên</th>
                                <th>Học phần</th>
                                <th>Số tiết</th>
                                <th>Đơn giá</th>
                                <th>HS Bằng cấp</th>
                                <th>HS Học phần</th>
                                <th>HS Sĩ số</th>
                                <th>Thành tiền (VNĐ)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payrolls as $payroll)
                                <tr>
                                    <td>{{ $payroll->teacher->full_name }}</td>
                                    <td>{{ $payroll->assignment->courseClass->course->name }}</td>
                                    <td>{{ $payroll->standard_periods_snapshot }}</td>
                                    <td>{{ number_format($payroll->base_pay_snapshot, 0, ',', '.') }}</td>
                                    <td>{{ number_format($payroll->degree_coeff_snapshot, 2) }}</td>
                                    <td>{{ number_format($payroll->course_coeff_snapshot, 2) }}</td>
                                    <td>{{ number_format($payroll->class_coeff_snapshot, 2) }}</td>
                                    <td><b>{{ number_format($payroll->total_amount, 0, ',', '.') }}</b></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Không có dữ liệu.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                 <div class="card-footer clearfix">
                    <div class="d-flex justify-content-end">
                        {{ $payrolls->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
