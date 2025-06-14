@extends('adminlte::page')

@section('title', 'Quản lý Lớp học phần')

@section('content_header')
    <h1 class="m-0 text-dark">Quản lý Lớp học phần</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">

        {{-- THÔNG BÁO --}}
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

        {{-- KHUNG DANH SÁCH --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
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

            <div class="card-body">

                {{-- TÌM KIẾM & LỌC --}}
                <div class="row mb-4 align-items-end">
                    {{-- Tìm kiếm --}}
                    <div class="col-md-6">
                        <form action="{{ route('classes.index') }}" method="GET">
                            <div class="form-group mb-0">
                                <label for="search">Tìm kiếm</label>
                                <div class="input-group">
                                    <input type="text" name="search" id="search" class="form-control"
                                           placeholder="Mã lớp, học phần, giảng viên..."
                                           value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        @if(request('search'))
                                            <a href="{{ route('classes.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Lọc theo kỳ học --}}
                    <div class="col-md-6">
                        <form action="{{ route('classes.index') }}" method="GET">
                            <div class="form-group mb-0">
                                <label for="term_id">Chọn kỳ học</label>
                                <div class="input-group">
                                    <select name="term_id" id="term_id" class="form-control" onchange="this.form.submit()">
                                        <option value="">Tất cả kỳ học</option>
                                        @foreach($terms as $term)
                                            <option value="{{ $term->id }}" {{ request('term_id') == $term->id ? 'selected' : '' }}>
                                                {{ $term->name }} ({{ $term->academic_year }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @if(request('term_id'))
                                        <div class="input-group-append">
                                            <a href="{{ route('classes.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- FORM XÓA NHIỀU --}}
                <form action="{{ route('classes.bulk_delete') }}" method="POST" id="bulk-delete-form">
                    @csrf
                    @method('DELETE')

                    {{-- Nút xóa nhiều --}}
                    <div class="mb-3">
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Xóa các lớp đã chọn?')">
                            <i class="fas fa-trash"></i> Xóa các lớp đã chọn
                        </button>
                    </div>

                    {{-- BẢNG --}}
                    <div class="table-responsive p-0">
                        <table class="table table-bordered table-hover text-nowrap">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 40px;"><input type="checkbox" id="check-all"></th>
                                    <th>ID</th>
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
                                        <td><input type="checkbox" name="selected_classes[]" value="{{ $courseClass->id }}"></td>
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
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa lớp học phần này?')">
                                                    <i class="fas fa-trash"></i> Xóa
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">Chưa có lớp học phần nào.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>

                {{-- PHÂN TRANG --}}
                <div class="card-footer d-flex justify-content-end">
                    {{ $classes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT CHỌN TẤT CẢ --}}
@push('js')
<script>
    document.getElementById('check-all').addEventListener('change', function () {
        const checkboxes = document.querySelectorAll('input[name="selected_classes[]"]');
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    });
</script>
@endpush

@stop
