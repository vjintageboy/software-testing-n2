@extends('adminlte::page')

@section('title', 'Bảng lương')

@section('content_header')
    <h1 class="m-0 text-dark">Lịch sử Bảng lương</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-check"></i> Thành công!</h5>
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <a href="{{ route('payrolls.create') }}" class="btn btn-primary mb-2">
                        <i class="fa fa-plus"></i> Tạo Bảng lương mới
                    </a>

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Kì học</th>
                                <th>Năm học</th>
                                <th>Số lượng thanh toán</th>
                                <th>Tổng tiền (VNĐ)</th>
                                <th>Ngày tính cuối cùng</th>
                                <th style="width: 200px;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payrollSummaries as $summary)
                                <tr>
                                    <td>{{ $summary->term->name }}</td>
                                    <td>{{ $summary->term->academic_year }}</td>
                                    <td>{{ $summary->total_records }}</td>
                                    <td>{{ number_format($summary->total_sum, 0, ',', '.') }}</td>
                                    <td>{{ $summary->last_calculated->format('d/m/Y H:i:s') }}</td>
                                    <td>
                                        <a href="{{ route('payrolls.show', $summary->term_id) }}" class="btn btn-sm btn-info">Xem chi tiết</a>
                                        <form action="{{ route('payrolls.destroy', $summary->term_id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa TOÀN BỘ bảng lương của kì học này không?')">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Chưa có bảng lương nào được tính.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@stop
