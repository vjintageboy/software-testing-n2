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
{{-- FORM xóa hàng loạt nằm ngoài bảng --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                {{-- Thông báo --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Đóng">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                {{-- Nút xóa hàng loạt --}}
                <div class="mb-3">
                    <form id="bulk-delete-form" action="{{ route('assignments.destroyBulk') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" id="bulk-delete-button" class="btn btn-danger" disabled>
                            <i class="fas fa-trash-alt mr-1"></i> Xóa mục đã chọn
                        </button>
                    </form>
                </div>

                {{-- Bảng dữ liệu --}}
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 50px;">
                                    <input type="checkbox" id="select-all-assignments">
                                </th>
                                <th style="width: 50px;">ID</th>
                                <th>Giảng viên</th>
                                <th>Lớp học phần</th>
                                <th style="width: 80px;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignments as $assignment)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="assignment_ids[]" class="assignment-checkbox" value="{{ $assignment->id }}" form="bulk-delete-form">
                                    </td>
                                    <td>{{ $assignment->id }}</td>
                                    <td>{{ $assignment->teacher->full_name ?? 'N/A' }}</td>
                                    <td>
                                        {{ $assignment->courseClass->course->name ?? 'N/A' }} 
                                        ({{ $assignment->courseClass->class_code ?? 'N/A' }})
                                    </td>
                                    <td>
                                        <a href="{{ route('assignments.edit', $assignment) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        <form action="{{ route('assignments.destroy', $assignment) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa phân công này?')">
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
            </div>

            {{-- Phân trang --}}
            <div class="card-footer d-flex justify-content-end">
                {{ $assignments->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Modal xác nhận xóa hàng loạt --}}
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" role="dialog" aria-labelledby="bulkDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận Xóa Phân công</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa <strong id="selected-assignments-count">0</strong> phân công đã chọn?</p>
                <p class="text-danger">Hành động này không thể hoàn tác.</p>
                <div class="form-group">
                    <label for="confirmation-text-input">Để xác nhận, vui lòng nhập <strong>"XÁC NHẬN"</strong>:</label>
                    <input type="text" class="form-control" id="confirmation-text-input">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirm-bulk-delete-button" disabled>Xóa</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Chọn tất cả
        $('#select-all-assignments').on('click', function() {
            $('.assignment-checkbox').prop('checked', $(this).prop('checked'));
            toggleBulkDeleteButton();
        });

        // Checkbox thay đổi
        $('.assignment-checkbox').on('change', function() {
            $('#select-all-assignments').prop('checked',
                $('.assignment-checkbox:checked').length === $('.assignment-checkbox').length);
            toggleBulkDeleteButton();
        });

        function toggleBulkDeleteButton() {
            const count = $('.assignment-checkbox:checked').length;
            $('#bulk-delete-button').prop('disabled', count === 0);
        }

        // Mở modal xác nhận
        $('#bulk-delete-button').on('click', function(e) {
            e.preventDefault();
            const count = $('.assignment-checkbox:checked').length;
            $('#selected-assignments-count').text(count);
            $('#bulkDeleteModal').modal('show');
        });

        // Xác nhận nội dung
        $('#confirmation-text-input').on('input', function() {
            $('#confirm-bulk-delete-button').prop('disabled', $(this).val() !== 'XÁC NHẬN');
        });

        // Gửi form khi xác nhận
        $('#confirm-bulk-delete-button').on('click', function() {
            $('#bulk-delete-form input[name="confirmation_text"]').remove();
            $('<input>', {
                type: 'hidden',
                name: 'confirmation_text',
                value: $('#confirmation-text-input').val()
            }).appendTo('#bulk-delete-form');

            $('#bulk-delete-form').submit();
        });
    });
</script>
@stop
