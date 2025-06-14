@extends('adminlte::page')

@section('title', 'Quản lý Phân công Giảng dạy')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0 text-dark">Quản lý Phân công Giảng dạy</h1>
    <a href="{{ route('assignments.create') }}" class="btn btn-success">
        <i class="fas fa-plus mr-1"></i> Thêm Phân công mới
    </a>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-12">

        {{-- Thông báo --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Đóng">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">

                {{-- Tìm kiếm và Lọc kỳ học --}}
                <div class="row mb-3 align-items-end">
                    <div class="col-md-6">
                        <form action="{{ route('assignments.index') }}" method="GET">
                            <label for="search">Tìm kiếm</label>
                            <div class="input-group">
                                <input type="text" name="search" class="form-control"
                                       placeholder="Tên giảng viên hoặc môn học..."
                                       value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    @if(request('search'))
                                        <a href="{{ route('assignments.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-6">
                        <form action="{{ route('assignments.index') }}" method="GET">
                            <label for="term_id">Chọn kỳ học</label>
                            <div class="input-group">
                                <select name="term_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">Tất cả kỳ học</option>
                                    @foreach($terms as $term)
                                        <option value="{{ $term->id }}" {{ request('term_id') == $term->id ? 'selected' : '' }}>
                                            {{ $term->name }} ({{ $term->academic_year }})
                                        </option>
                                    @endforeach
                                </select>
                                @if(request('term_id'))
                                    <div class="input-group-append">
                                        <a href="{{ route('assignments.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Nút xóa nhiều (gắn form) --}}
                <form id="bulk-delete-form" action="{{ route('assignments.destroyBulk') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="mb-3">
                        <button type="button" id="bulk-delete-button" class="btn btn-danger" disabled>
                            <i class="fas fa-trash-alt mr-1"></i> Xóa mục đã chọn
                        </button>
                    </div>

                    {{-- Bảng --}}
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 50px;"><input type="checkbox" id="select-all-assignments"></th>
                                    <th style="width: 50px;">ID</th>
                                    <th>Giảng viên</th>
                                    <th>Lớp học phần</th>
                                    <th style="width: 150px;">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assignments as $assignment)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="assignment_ids[]"
                                                   class="assignment-checkbox" value="{{ $assignment->id }}">
                                        </td>
                                        <td>{{ $assignment->id }}</td>
                                        <td>{{ $assignment->teacher->full_name ?? 'N/A' }}</td>
                                        <td>{{ $assignment->courseClass->course->name ?? 'N/A' }}
                                            ({{ $assignment->courseClass->class_code ?? 'N/A' }})
                                        </td>
                                        <td>
                                            <a href="{{ route('assignments.edit', $assignment) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i> Sửa
                                            </a>
                                            <form action="{{ route('assignments.destroy', $assignment) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa?')">
                                                    <i class="fas fa-trash"></i> Xóa
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Không có dữ liệu phân công.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>

            <div class="card-footer d-flex justify-content-end">
                {{ $assignments->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Modal xác nhận --}}
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận Xóa Phân công</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa <strong id="selected-assignments-count">0</strong> phân công?</p>
                <p class="text-danger">Hành động này không thể hoàn tác.</p>
                <label>Nhập <b>"XÁC NHẬN"</b> để xác nhận:</label>
                <input type="text" id="confirmation-text-input" class="form-control">
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button class="btn btn-danger" id="confirm-bulk-delete-button" disabled>Xóa</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(function () {
        // Chọn tất cả
        $('#select-all-assignments').on('click', function () {
            $('.assignment-checkbox').prop('checked', this.checked);
            toggleBulkDeleteButton();
        });

        $('.assignment-checkbox').on('change', function () {
            const all = $('.assignment-checkbox').length;
            const checked = $('.assignment-checkbox:checked').length;
            $('#select-all-assignments').prop('checked', all === checked);
            toggleBulkDeleteButton();
        });

        function toggleBulkDeleteButton() {
            $('#bulk-delete-button').prop('disabled', $('.assignment-checkbox:checked').length === 0);
        }

        // Mở modal
        $('#bulk-delete-button').on('click', function () {
            const count = $('.assignment-checkbox:checked').length;
            $('#selected-assignments-count').text(count);
            $('#confirmation-text-input').val('');
            $('#confirm-bulk-delete-button').prop('disabled', true);
            $('#bulkDeleteModal').modal('show');
        });

        $('#confirmation-text-input').on('input', function () {
            $('#confirm-bulk-delete-button').prop('disabled', $(this).val() !== 'XÁC NHẬN');
        });

        $('#confirm-bulk-delete-button').on('click', function () {
            $('<input>', {
                type: 'hidden',
                name: 'confirmation_text',
                value: 'XÁC NHẬN'
            }).appendTo('#bulk-delete-form');
            $('#bulk-delete-form').submit();
        });
    });
</script>
@stop
