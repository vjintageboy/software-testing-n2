@extends('adminlte::page')

@section('title', 'Quản lý Lớp học phần')

@section('content_header')
    <h1 class="m-0 text-dark">Quản lý Lớp học phần</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        {{-- THÔNG BÁO THÀNH CÔNG / LỖI --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        {{-- KHUNG BẢNG DANH SÁCH --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Danh sách Lớp học phần</h3>
                <div class="btn-group">
                    <a href="{{ route('classes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Thêm Lớp mới
                    </a>
                    <a href="{{ route('classes.create_bulk') }}" class="btn btn-info">
                        <i class="fas fa-layer-group"></i> Tạo nhanh nhiều lớp
                    </a>
                </div>
            </div>

            <div class="card-body table-responsive p-0">
                <table class="table table-bordered table-hover text-nowrap">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px;">ID</th>
                            <th>Mã Lớp</th>
                            <th>Học phần</th>
                            <th>Học kỳ</th>
                            <th>Giảng viên</th>
                            <th>Sĩ số</th>
                            <th>Hệ số sĩ số</th>
                            <th style="width: 150px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classes as $courseClass)
                            <tr>
                                <td>{{ $courseClass->id }}</td>
                                <td>{{ $courseClass->class_code }}</td>
                                <td>{{ $courseClass->course->name ?? '-' }}</td>
                                <td>{{ $courseClass->term->name ?? '-' }}</td>
                                <td>{{ $courseClass->teacher_name ?? '-' }}</td>
                                <td>{{ $courseClass->number_of_students }}</td>
                                <td>{{ $courseClass->css_coefficient }}</td>
                                <td>
                                    <a href="{{ route('classes.edit', $courseClass->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <form action="{{ route('classes.destroy', $courseClass->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa lớp học phần này?')">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">Chưa có lớp học phần nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PHÂN TRANG --}}
            <div class="card-footer d-flex justify-content-end">
                {{ $classes->links() }}
            </div>
        </div>
    </div>
</div>
@stop
