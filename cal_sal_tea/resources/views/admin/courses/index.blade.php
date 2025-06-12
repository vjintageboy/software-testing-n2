@extends('adminlte::page')

@section('title', 'Quản lý Học phần')

@section('content_header')
    <h1 class="m-0 text-dark">Quản lý Học phần</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-check"></i> Thành công!</h5>
                            {{ session('success') }}
                        </div>
                    @endif
                     @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-ban"></i> Lỗi!</h5>
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <a href="{{ route('courses.create') }}" class="btn btn-primary mb-2">
                        <i class="fa fa-plus"></i> Tạo Học phần mới
                    </a>

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Mã HP</th>
                                <th>Tên Học phần</th>
                                <th>Số tín chỉ</th> {{-- Thêm cột mới --}}
                                <th>Số tiết</th>
                                <th>Hệ số</th>
                                <th style="width: 150px;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($courses as $course)
                                <tr>
                                    <td>{{ $course->course_code }}</td>
                                    <td>{{ $course->name }}</td>
                                    <td>{{ $course->credits }}</td> {{-- Hiển thị dữ liệu mới --}}
                                    <td>{{ $course->standard_periods }}</td>
                                    <td>{{ $course->coefficient }}</td>
                                    <td>
                                        <a href="{{ route('courses.edit', $course) }}" class="btn btn-sm btn-info">Sửa</a>
                                        <form action="{{ route('courses.destroy', $course) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Không có dữ liệu.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-3 d-flex justify-content-end">
                        {{ $courses->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
@stop
