@extends('adminlte::page')

@section('title', 'Thêm quy tắc Hệ số')

@section('content_header')
    <h1 class="m-0 text-dark">Thêm quy tắc Hệ số mới</h1>
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

                    <form action="{{ route('class-size-coefficients.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="min_students">Sĩ số tối thiểu</label>
                            <input type="number" name="min_students" class="form-control" id="min_students" value="{{ old('min_students') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="max_students">Sĩ số tối đa</label>
                            <input type="number" name="max_students" class="form-control" id="max_students" value="{{ old('max_students') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="coefficient">Hệ số</label>
                            <input type="number" step="0.01" name="coefficient" class="form-control" id="coefficient" value="{{ old('coefficient') }}" required>
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
                            <input type="text" name="description" class="form-control" id="description" placeholder="Ví dụ: Lớp đông" value="{{ old('description') }}">
                        </div>

                        <button type="submit" class="btn btn-primary">Lưu</button>
                        <a href="{{ route('class-size-coefficients.index') }}" class="btn btn-secondary">Hủy</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
