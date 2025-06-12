@extends('adminlte::page')

@section('title', 'Quản lý Giáo viên')

@section('content_header')
    <h1 class="m-0 text-dark">Quản lý Giáo viên</h1>
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
                    
                    <a href="{{ route('teachers.create') }}" class="btn btn-primary mb-2">
                        <i class="fa fa-plus"></i> Thêm Giáo viên
                    </a>

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Mã GV</th>
                                <th>Họ và tên</th>
                                <th>Khoa</th>
                                <th>Bằng cấp</th>
                                <th>Email</th>
                                <th>Trạng thái</th>
                                <th style="width: 150px;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teachers as $teacher)
                                <tr>
                                    <td>{{ $teacher->teacher_code }}</td>
                                    <td>{{ $teacher->full_name }}</td>
                                    <td>{{ $teacher->faculty->name ?? 'N/A' }}</td>
                                    <td>{{ $teacher->degree->name ?? 'N/A' }}</td>
                                    <td>{{ $teacher->email }}</td>
                                    <td>
                                        @if($teacher->is_active)
                                            <span class="badge badge-success">Hoạt động</span>
                                        @else
                                            <span class="badge badge-danger">Đã nghỉ</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-sm btn-info">Sửa</a>
                                        <form action="{{ route('teachers.destroy', $teacher) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Không có dữ liệu.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-3 d-flex justify-content-end">
                        {{ $teachers->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
@stop
