@extends('adminlte::page')

@section('title', 'Quản lý Lớp học phần')

@section('content_header')
    <h1 class="m-0 text-dark">Quản lý Lớp học phần</h1>
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
                    
                    <a href="{{ route('classes.create') }}" class="btn btn-primary mb-2">
                        <i class="fa fa-plus"></i> Mở Lớp học phần mới
                    </a>

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Mã Lớp HP</th>
                                <th>Tên Học phần</th>
                                <th>Kì học</th>
                                <th>Sĩ số</th>
                                <th style="width: 150px;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classes as $class)
                                <tr>
                                    <td>{{ $class->class_code }}</td>
                                    <td>{{ $class->course->name ?? 'N/A' }}</td>
                                    <td>{{ $class->term->name ?? 'N/A' }} ({{$class->term->academic_year ?? 'N/A'}})</td>
                                    <td>{{ $class->number_of_students }}</td>
                                    <td>
                                        <a href="{{ route('classes.edit', $class) }}" class="btn btn-sm btn-info">Sửa</a>
                                        <form action="{{ route('classes.destroy', $class) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Không có dữ liệu.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-3 d-flex justify-content-end">
                        {{ $classes->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
@stop
