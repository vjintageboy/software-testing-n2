@extends('adminlte::page')

@section('title', 'Phân công Giảng dạy')

@section('content_header')
    <h1 class="m-0 text-dark">Phân công Giảng dạy</h1>
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
                    
                    <a href="{{ route('assignments.create') }}" class="btn btn-primary mb-2">
                        <i class="fa fa-plus"></i> Tạo phân công mới
                    </a>

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Mã Lớp HP</th>
                                <th>Tên Học phần</th>
                                <th>Giáo viên phụ trách</th>
                                <th>Khoa</th>
                                <th>Kì học</th>
                                <th style="width: 150px;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignments as $assignment)
                                <tr>
                                    <td>{{ $assignment->courseClass->class_code ?? 'N/A' }}</td>
                                    <td>{{ $assignment->courseClass->course->name ?? 'N/A' }}</td>
                                    <td>{{ $assignment->teacher->full_name ?? 'N/A' }}</td>
                                    <td>{{ $assignment->teacher->faculty->abbreviation ?? 'N/A' }}</td>
                                    <td>{{ $assignment->courseClass->term->name ?? 'N/A' }} ({{$assignment->courseClass->term->academic_year ?? 'N/A'}})</td>
                                    <td>
                                        <a href="{{ route('assignments.edit', $assignment) }}" class="btn btn-sm btn-info">Sửa</a>
                                        <form action="{{ route('assignments.destroy', $assignment) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn hủy phân công này?')">Hủy</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Chưa có phân công nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-3 d-flex justify-content-end">
                        {{ $assignments->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
@stop
