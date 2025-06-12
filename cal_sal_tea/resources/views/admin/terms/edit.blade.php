@extends('adminlte::page')

@section('title', 'Chỉnh sửa Kì học')

@section('content_header')
    <h1 class="m-0 text-dark">Chỉnh sửa Kì học</h1>
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

                    <form action="{{ route('terms.update', $term) }}" method="POST">
                        @csrf
                        @method('PUT')
                         <div class="form-group">
                            <label for="name">Tên Kì học</label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="Ví dụ: Học kỳ 1" value="{{ old('name', $term->name) }}" required>
                        </div>
                         <div class="form-group">
                            <label for="academic_year">Năm học</label>
                            <input type="text" name="academic_year" class="form-control" id="academic_year" placeholder="Ví dụ: 2024-2025" value="{{ old('academic_year', $term->academic_year) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="start_date">Ngày bắt đầu</label>
                            <input type="date" name="start_date" class="form-control" id="start_date" value="{{ old('start_date', $term->start_date) }}" required>
                        </div>
                         <div class="form-group">
                            <label for="end_date">Ngày kết thúc</label>
                            <input type="date" name="end_date" class="form-control" id="end_date" value="{{ old('end_date', $term->end_date) }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                        <a href="{{ route('terms.index') }}" class="btn btn-secondary">Hủy</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
