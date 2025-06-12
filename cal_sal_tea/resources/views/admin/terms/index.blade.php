@extends('adminlte::page')

@section('title', 'Quản lý Kì học')

@section('content_header')
    <h1 class="m-0 text-dark">Quản lý Kì học</h1>
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
                    
                    <a href="{{ route('terms.create') }}" class="btn btn-primary mb-2">
                        <i class="fa fa-plus"></i> Tạo Kì học mới
                    </a>

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th>Tên Kì học</th>
                                <th>Năm học</th>
                                <th>Ngày bắt đầu</th>
                                <th>Ngày kết thúc</th>
                                <th style="width: 150px;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($terms as $term)
                                <tr>
                                    <td>{{ $term->id }}</td>
                                    <td>{{ $term->name }}</td>
                                    <td>{{ $term->academic_year }}</td>
                                    <td>{{ \Carbon\Carbon::parse($term->start_date)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($term->end_date)->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('terms.edit', $term) }}" class="btn btn-sm btn-info">Sửa</a>
                                        <form action="{{ route('terms.destroy', $term) }}" method="POST" class="d-inline">
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
                        {{ $terms->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
@stop
