@extends('adminlte::page')

@section('title', 'Tạo Bảng lương mới')

@section('content_header')
    <h1 class="m-0 text-dark">Tạo Bảng lương mới</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Chọn kì học để tính lương</h3>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                           {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('payrolls.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="term_id">Kì học</label>
                            <select name="term_id" id="term_id" class="form-control" required>
                                <option value="">-- Chọn Kì học --</option>
                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}">{{ $term->name }} - {{ $term->academic_year }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="alert alert-warning">
                            <p><b>Lưu ý:</b> Việc tính lương sẽ xóa toàn bộ bảng lương cũ của kì học đã chọn (nếu có) và tính toán lại từ đầu dựa trên các phân công và tham số hiện tại.</p>
                        </div>
                        
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn tính lương cho kì học này? Hành động này không thể hoàn tác.')">
                            <i class="fas fa-calculator"></i> Bắt đầu Tính lương
                        </button>
                        <a href="{{ route('payrolls.index') }}" class="btn btn-secondary">Hủy</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
