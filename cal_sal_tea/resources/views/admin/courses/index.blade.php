@extends('adminlte::page')

@section('title', 'Danh sách Học phần')

@section('content_header')
    <h1 class="m-0 text-dark">Danh sách Học phần</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card>
                <div class="mb-3">
                    <a href="{{ route('courses.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Thêm mới
                    </a>
                </div>
                
                @include('includes.alert')

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 5%">#</th>
                            <th>Mã Học phần</th>
                            <th>Tên Học phần</th>
                            <th>Số tín chỉ</th>
                            <th>Khoa</th> {{-- Thêm cột Khoa --}}
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
                                {{-- Hiển thị tên Khoa, có kiểm tra nếu null --}}
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
                 <div class="mt-3">
                    {{ $courses->links() }}
                </div>
            </x-adminlte-card>
        </div>
    </div>
@stop
