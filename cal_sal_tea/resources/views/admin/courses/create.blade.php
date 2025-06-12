@extends('adminlte::page')

@section('title', 'Tạo Học phần mới')

@section('content_header')
    <h1 class="m-0 text-dark">Tạo Học phần mới</h1>
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

                    <form action="{{ route('courses.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="course_code">Mã Học phần</label>
                            <input type="text" name="course_code" class="form-control" id="course_code" placeholder="Ví dụ: IT4409" value="{{ old('course_code') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="name">Tên Học phần</label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="Ví dụ: An toàn và an ninh mạng" value="{{ old('name') }}" required>
                        </div>
                        {{-- Thêm ô nhập liệu mới --}}
                        <div class="form-group">
                            <label for="credits">Số tín chỉ</label>
                            <input type="number" name="credits" class="form-control" id="credits" placeholder="Ví dụ: 3" value="{{ old('credits') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="standard_periods">Số tiết quy chuẩn</label>
                            <input type="number" name="standard_periods" class="form-control" id="standard_periods" placeholder="Ví dụ: 45" value="{{ old('standard_periods') }}" required>
                        </div>
                         <div class="form-group">
                            <label for="coefficient">Hệ số học phần</label>
                            <input type="number" step="0.01" name="coefficient" class="form-control" id="coefficient" value="{{ old('coefficient', '1.00') }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                        <a href="{{ route('courses.index') }}" class="btn btn-secondary">Hủy</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
