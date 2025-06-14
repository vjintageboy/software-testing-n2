@extends('adminlte::page')

@section('title', 'Quản lý Bằng cấp')

@section('content_header')
    <h1 class="m-0 text-dark">Quản lý Bằng cấp</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    {{-- Thông báo thành công / lỗi --}}
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
                    
                    {{-- Thanh tìm kiếm + nút tạo mới --}}
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-8">
                            <form action="{{ route('degrees.index') }}" method="GET">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên bằng cấp..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Tìm kiếm
                                        </button>
                                        @if(request('search'))
                                            <a href="{{ route('degrees.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-times"></i> Xóa
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4 text-md-right mt-2 mt-md-0">
                            <a href="{{ route('degrees.create') }}" class="btn btn-success">
                                <i class="fa fa-plus"></i> Tạo Bằng cấp mới
                            </a>
                        </div>
                    </div>

                    {{-- Bảng dữ liệu --}}
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th>Tên Bằng cấp</th>
                                <th>Tên viết tắt</th>
                                <th>Hệ số</th>
                                <th style="width: 150px;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($degrees as $degree)
                                <tr>
                                    <td>{{ $degree->id }}</td>
                                    <td>{{ $degree->name }}</td>
                                    <td>{{ $degree->abbreviation }}</td>
                                    <td>{{ $degree->coefficient }}</td>
                                    <td>
                                        <a href="{{ route('degrees.edit', $degree) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        <form action="{{ route('degrees.destroy', $degree) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa bằng cấp này?')">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
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

                    {{-- Phân trang --}}
                    <div class="mt-3 d-flex justify-content-end">
                        {{ $degrees->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
@stop
