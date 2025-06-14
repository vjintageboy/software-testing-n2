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

                {{-- Thông báo --}}
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

                {{-- Tìm kiếm + Tạo mới --}}
                <div class="row mb-3 align-items-center">
                    <div class="col-md-8">
                        <form action="{{ route('terms.index') }}" method="GET">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên kì học..." value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Tìm kiếm
                                    </button>
                                    @if(request('search'))
                                        <a href="{{ route('terms.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Xóa
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4 text-md-right mt-2 mt-md-0">
                        <a href="{{ route('terms.create') }}" class="btn btn-success">
                            <i class="fa fa-plus"></i> Tạo Kì học mới
                        </a>
                    </div>
                </div>

                {{-- Bảng danh sách --}}
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
                                    <a href="{{ route('terms.edit', $term) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <form action="{{ route('terms.destroy', $term) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa kì học này?')">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
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

                {{-- Phân trang --}}
                <div class="mt-3 d-flex justify-content-end">
                    {{ $terms->links() }}
                </div>

            </div>
        </div>
    </div>
</div>
@stop
