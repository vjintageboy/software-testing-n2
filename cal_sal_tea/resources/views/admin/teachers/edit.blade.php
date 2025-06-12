@extends('adminlte::page')

@section('title', 'Chỉnh sửa thông tin Giáo viên')

@section('content_header')
    <h1 class="m-0 text-dark">Chỉnh sửa thông tin Giáo viên</h1>
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

                    <form action="{{ route('teachers.update', $teacher) }}" method="POST">
                        @csrf
                        @method('PUT')
                         <div class="form-group">
                            <label for="teacher_code">Mã giáo viên</label>
                            <input type="text" name="teacher_code" class="form-control" id="teacher_code" value="{{ old('teacher_code', $teacher->teacher_code) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="full_name">Họ và tên</label>
                            <input type="text" name="full_name" class="form-control" id="full_name" value="{{ old('full_name', $teacher->full_name) }}" required>
                        </div>
                         <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control" id="email" value="{{ old('email', $teacher->email) }}">
                        </div>
                         <div class="form-group">
                            <label for="phone">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" id="phone" value="{{ old('phone', $teacher->phone) }}">
                        </div>
                        <div class="form-group">
                            <label for="date_of_birth">Ngày sinh</label>
                            <input type="date" name="date_of_birth" class="form-control" id="date_of_birth" value="{{ old('date_of_birth', $teacher->date_of_birth) }}">
                        </div>
                        <div class="form-group">
                            <label for="faculty_id">Khoa</label>
                            <select name="faculty_id" id="faculty_id" class="form-control" required>
                                <option value="">-- Chọn Khoa --</option>
                                @foreach($faculties as $faculty)
                                    <option value="{{ $faculty->id }}" {{ old('faculty_id', $teacher->faculty_id) == $faculty->id ? 'selected' : '' }}>{{ $faculty->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="degree_id">Bằng cấp</label>
                            <select name="degree_id" id="degree_id" class="form-control" required>
                                <option value="">-- Chọn Bằng cấp --</option>
                                @foreach($degrees as $degree)
                                    <option value="{{ $degree->id }}" {{ old('degree_id', $teacher->degree_id) == $degree->id ? 'selected' : '' }}>{{ $degree->name }}</option>
                                @endforeach
                            </select>
                        </div>
                         <div class="form-group">
                            <label for="is_active">Trạng thái</label>
                            <select name="is_active" id="is_active" class="form-control">
                                <option value="1" {{ old('is_active', $teacher->is_active) == 1 ? 'selected' : '' }}>Hoạt động</option>
                                <option value="0" {{ old('is_active', $teacher->is_active) == 0 ? 'selected' : '' }}>Đã nghỉ</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                        <a href="{{ route('teachers.index') }}" class="btn btn-secondary">Hủy</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
