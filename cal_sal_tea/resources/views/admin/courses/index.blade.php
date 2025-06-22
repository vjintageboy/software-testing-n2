@extends('adminlte::page')

@section('title', 'Danh sách Học phần')

@section('content_header')
    <h1 class="m-0 text-dark">Danh sách Học phần</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card>

                {{-- Thanh công cụ tìm kiếm và thêm mới --}}
                <div class="row mb-3 align-items-center">
                    {{-- Form tìm kiếm --}}
                    <div class="col-md-6">
                        <form action="{{ route('courses.index') }}" method="GET">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên hoặc mã học phần..." value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Tìm
                                    </button>
                                    @if(request('search'))
                                        <a href="{{ route('courses.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Xóa
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Lọc kỳ học + Nút Thêm mới --}}
                    <div class="col-md-6">
                        <div class="d-flex justify-content-end align-items-center">
                            <form action="{{ route('courses.index') }}" method="GET" class="form-inline mr-2">
                                <div class="input-group">
                                    <select name="term_id" class="form-control" onchange="this.form.submit()">
                                        <option value="">Tất cả kỳ học</option>
                                        @foreach($terms as $term)
                                            <option value="{{ $term->id }}" {{ request('term_id') == $term->id ? 'selected' : '' }}>
                                                {{ $term->name }} ({{ $term->academic_year }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @if(request('term_id'))
                                        <div class="input-group-append">
                                            <a href="{{ route('courses.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-times"></i> Xóa
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </form>
                            <a href="{{ route('courses.create') }}" class="btn btn-success">
                                <i class="fas fa-plus"></i> Thêm mới
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Thông báo --}}
                @include('includes.alert')

                {{-- Bảng dữ liệu --}}
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 5%">STT</th>
                            <th>Mã Học phần</th>
                            <th>Tên Học phần</th>
                            <th>Số tín chỉ</th>
                            <th>Khoa</th>
                            <th style="width: 15%">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($courses as $course)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $course->course_code }}</td>
                                <td>{{ $course->name }}</td>
                                <td>{{ $course->credits }}</td>
                                <td>{{ $course->faculty->name ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('courses.edit', $course) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <form action="{{ route('courses.destroy', $course) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa học phần này?')">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Phân trang --}}
                <div class="mt-3">
                    {{ $courses->links() }}
                </div>

            </x-adminlte-card>
        </div>
    </div>
@stop
