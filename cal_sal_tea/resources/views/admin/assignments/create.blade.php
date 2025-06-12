@extends('adminlte::page')

@section('title', 'Tạo phân công mới')

@section('content_header')
    <h1 class="m-0 text-dark">Tạo phân công mới</h1>
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

                    <form action="{{ route('assignments.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="course_class_id">Lớp học phần (chỉ hiển thị các lớp chưa được phân công)</label>
                            <select name="course_class_id" id="course_class_id" class="form-control" required>
                                <option value="">-- Chọn Lớp học phần --</option>
                                @foreach($unassignedClasses as $class)
                                    <option value="{{ $class->id }}" {{ old('course_class_id') == $class->id ? 'selected' : '' }}>
                                        [{{ $class->class_code }}] {{ $class->course->name }} - ({{ $class->term->name }} {{ $class->term->academic_year }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="teacher_id">Giáo viên</label>
                            <select name="teacher_id" id="teacher_id" class="form-control" required>
                                <option value="">-- Chọn Giáo viên --</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->full_name }} ({{ $teacher->teacher_code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="notes">Ghi chú</label>
                            <textarea name="notes" class="form-control" id="notes" rows="3">{{ old('notes') }}</textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Phân công</button>
                        <a href="{{ route('assignments.index') }}" class="btn btn-secondary">Hủy</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
