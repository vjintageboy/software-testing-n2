    @extends('adminlte::page')

    @section('title', 'Quản lý Khoa')

    @section('content_header')
        <h1 class="m-0 text-dark">Quản lý Khoa</h1>
    @stop

    @section('content')
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        {{-- Hiển thị thông báo --}}
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
                        
                        {{-- Nút tạo mới --}}
                        <a href="{{ route('faculties.create') }}" class="btn btn-primary mb-2">
                            <i class="fa fa-plus"></i> Tạo Khoa mới
                        </a>

                        {{-- Bảng dữ liệu --}}
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">ID</th>
                                    <th>Tên Khoa</th>
                                    <th>Viết tắt</th>
                                    <th>Mô tả</th>
                                    <th style="width: 150px;">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($faculties as $faculty)
                                    <tr>
                                        <td>{{ $faculty->id }}</td>
                                        <td>{{ $faculty->name }}</td>
                                        <td>{{ $faculty->abbreviation }}</td>
                                        <td>{{ $faculty->description }}</td>
                                        <td>
                                            <a href="{{ route('faculties.edit', $faculty) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i> Sửa
                                            </a>
                                            <form action="{{ route('faculties.destroy', $faculty) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa khoa này?')">
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
                            {{ $faculties->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @stop
    