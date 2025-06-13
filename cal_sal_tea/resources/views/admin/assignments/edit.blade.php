@extends('adminlte::page')

@section('title', 'Chỉnh sửa Phân công')

@section('content_header')
    <h1 class="m-0 text-dark">Chỉnh sửa Phân công</h1>
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

                    <form action="{{ route('assignments.update', $assignment) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>Lớp học phần</label>
                            <input type="text" class="form-control" 
                                   value="[{{ $assignment->courseClass->class_code ?? 'N/A' }}] {{ $assignment->courseClass->course->name ?? 'N/A' }} - ({{ $assignment->courseClass->term->name ?? 'N/A' }} {{ $assignment->courseClass->term->academic_year ?? 'N/A' }})" 
                                   readonly>
                            {{-- Giữ lại ID lớp học phần nếu cần cho logic khác, nhưng không cho phép sửa trực tiếp ở đây --}}
                            <input type="hidden" name="course_class_id" value="{{ $assignment->course_class_id }}">
                        </div>
                        
                        <div class="form-group">
                            <label for="teacher_id">Giáo viên</label>
                            <select name="teacher_id" id="teacher_id" class="form-control" required>
                                <option value="">-- Chọn Giáo viên --</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id', $assignment->teacher_id) == $teacher->id ? 'selected' : '' }}>{{ $teacher->full_name }} ({{ $teacher->teacher_code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="notes">Ghi chú</label>
                            <textarea name="notes" class="form-control" id="notes" rows="3">{{ old('notes', $assignment->notes) }}</textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                        <a href="{{ route('assignments.index') }}" class="btn btn-secondary">Hủy</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
