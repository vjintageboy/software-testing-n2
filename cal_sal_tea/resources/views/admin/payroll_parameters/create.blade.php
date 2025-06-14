@extends('adminlte::page')

@section('title', 'Thêm Đơn giá mới')

@section('content_header')
    <h1 class="m-0 text-dark">Thêm Đơn giá mới</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('payroll-parameters.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="base_pay_per_period">Đơn giá / Tiết (VNĐ)</label>
                            <input type="number" name="base_pay_per_period" class="form-control" id="base_pay_per_period" value="{{ old('base_pay_per_period') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="valid_from">Ngày có hiệu lực</label>
                            <input type="date" name="valid_from" class="form-control" id="valid_from" value="{{ old('valid_from') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="valid_to">Ngày hết hiệu lực</label>
                            <input type="date" name="valid_to" class="form-control" id="valid_to" value="{{ old('valid_to') }}">
                            <small class="form-text text-muted">Để trống nếu đang áp dụng</small>
                        </div>
                        <div class="form-group">
                            <label for="description">Mô tả</label>
                            <input type="text" name="description" class="form-control" id="description" placeholder="Ví dụ: Áp dụng cho năm 2025" value="{{ old('description') }}">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Lưu</button>
                        <a href="{{ route('payroll-parameters.index') }}" class="btn btn-secondary">Hủy</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
