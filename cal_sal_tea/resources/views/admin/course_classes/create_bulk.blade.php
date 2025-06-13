{{-- File: resources/views/admin/course_classes/create_bulk.blade.php --}}
@extends('adminlte::page')

@section('title', 'Tạo nhanh Lớp học phần')

@section('content_header')
    <h1>Tạo nhanh Lớp học phần</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('classes.store_bulk') }}" method="POST">
            @csrf

            {{-- Chọn Học phần --}}
            <div class="form-group">
                <label for="course_id">Chọn Học phần</label>
                <select name="course_id" id="course_id" class="form-control @error('course_id') is-invalid @enderror">
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->name }} ({{ $course->course_code }})</option>
                    @endforeach
                </select>
                @error('course_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {{-- Chọn Học kỳ --}}
            <div class="form-group">
                <label for="term_id">Chọn Học kỳ</label>
                <select name="term_id" id="term_id" class="form-control @error('term_id') is-invalid @enderror">
                    @foreach($terms as $term)
                        <option value="{{ $term->id }}">{{ $term->name }}</option>
                    @endforeach
                </select>
                @error('term_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {{-- Nhập sĩ số --}}
            <div class="form-group">
                <label for="number_of_students">Sĩ số mặc định</label>
                <input type="number" name="number_of_students" id="number_of_students" class="form-control @error('number_of_students') is-invalid @enderror" value="{{ old('number_of_students', 45) }}">
                @error('number_of_students')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {{-- Nhập số lượng lớp --}}
            <div class="form-group">
                <label for="number_of_classes">Số lượng lớp cần tạo</label>
                <input type="number" name="number_of_classes" id="number_of_classes" class="form-control @error('number_of_classes') is-invalid @enderror" value="{{ old('number_of_classes', 10) }}">
                @error('number_of_classes')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <button type="submit" class="btn btn-success">Tạo các lớp</button>
            <a href="{{ route('classes.index') }}" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</div>
@stop