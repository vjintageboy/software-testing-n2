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
                    
                    <a href="{{ route('degrees.create') }}" class="btn btn-primary mb-2">
                        <i class="fa fa-plus"></i> Tạo Bằng cấp mới
                    </a>

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th>Tên Bằng cấp</th>
                                <th>Tên viết tắt</th> {{-- Thêm cột mới --}}
                                <th>Hệ số</th>
                                <th style="width: 150px;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($degrees as $degree)
                                <tr>
                                    <td>{{ $degree->id }}</td>
                                    <td>{{ $degree->name }}</td>
                                    <td>{{ $degree->abbreviation }}</td> {{-- Hiển thị dữ liệu mới --}}
                                    <td>{{ $degree->coefficient }}</td>
                                    <td>
                                        <a href="{{ route('degrees.edit', $degree) }}" class="btn btn-sm btn-info">Sửa</a>
                                        <form action="{{ route('degrees.destroy', $degree) }}" method="POST" class="d-inline">
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
                        {{ $degrees->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
@stop
