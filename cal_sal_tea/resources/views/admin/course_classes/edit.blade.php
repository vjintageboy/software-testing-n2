@extends('adminlte::page')

@section('title', 'Chỉnh sửa Lớp học phần')

@section('content_header')
    <h1 class="m-0 text-dark">Chỉnh sửa Lớp học phần</h1>
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

                    <form action="{{ route('classes.update', ['class' => $class]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="class_code">Mã Lớp học phần</label>
                            <input type="text" name="class_code" class="form-control" id="class_code" value="{{ old('class_code', $class->class_code) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="course_id">Học phần</label>
                            <select name="course_id" id="course_id" class="form-control" required>
                                <option value="">-- Chọn Học phần --</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id', $class->course_id) == $course->id ? 'selected' : '' }}>{{ $course->name }} ({{$course->course_code}})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="term_id">Kì học</label>
                            <select name="term_id" id="term_id" class="form-control" required>
                                <option value="">-- Chọn Kì học --</option>
                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}" {{ old('term_id', $class->term_id) == $term->id ? 'selected' : '' }}>{{ $term->name }} - {{ $term->academic_year }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="number_of_students">Sĩ số</label>
                            <input type="number" name="number_of_students" class="form-control" id="number_of_students" value="{{ old('number_of_students', $class->number_of_students) }}" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                        <a href="{{ route('classes.index') }}" class="btn btn-secondary">Hủy</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
