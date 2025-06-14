@extends('adminlte::page')

@section('title', 'Đơn giá Tiết dạy')

@section('content_header')
    <h1 class="m-0 text-dark">Đơn giá Tiết dạy</h1>
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
                    
                    <a href="{{ route('payroll-parameters.create') }}" class="btn btn-primary mb-2">
                        <i class="fa fa-plus"></i> Thêm Đơn giá mới
                    </a>

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Đơn giá / Tiết (VNĐ)</th>
                                <th>Hiệu lực từ</th>
                                <th>Hiệu lực đến</th>
                                <th>Mô tả</th>
                                <th style="width: 150px;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($parameters as $parameter)
                                <tr>
                                    <td>{{ number_format($parameter->base_pay_per_period, 0, ',', '.') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($parameter->valid_from)->format('d/m/Y') }}</td>
                                    <td>{{ $parameter->valid_to ? \Carbon\Carbon::parse($parameter->valid_to)->format('d/m/Y') : 'Đang áp dụng' }}</td>
                                    <td>{{ $parameter->description }}</td>
                                    <td>
                                        <a href="{{ route('payroll-parameters.edit', $parameter) }}" class="btn btn-sm btn-info">Sửa</a>
                                        <form action="{{ route('payroll-parameters.destroy', $parameter) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa đơn giá này?')">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Không có dữ liệu.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@stop
