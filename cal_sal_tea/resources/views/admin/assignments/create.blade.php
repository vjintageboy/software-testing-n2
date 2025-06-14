@extends('adminlte::page')

@section('title', 'Thêm Phân công mới')

@section('content_header')
    <h1 class="m-0 text-dark">Thêm Phân công mới</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">

        {{-- Form tìm kiếm lớp học phần (nằm ngoài form chính) --}}
        <div class="card mb-3">
            <div class="card-body">
                <form action="{{ route('assignments.create') }}" method="GET" class="form-inline">
                    <div class="input-group w-100">
                        <input type="text" name="search" class="form-control" placeholder="Tìm theo mã lớp hoặc tên học phần..." value="{{ request('search') }}">
                        <select name="term_id" class="form-control" onchange="this.form.submit()">
                            <option value="">Tất cả kỳ học</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" {{ request('term_id') == $term->id ? 'selected' : '' }}>
                                    {{ $term->name }} ({{ $term->academic_year }})
                                </option>
                            @endforeach
                        </select>
                        <div class="input-group-append">
                            

                            @if(request('search') || request('term_id'))
                                <a href="{{ route('assignments.create') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Form chính để lưu phân công --}}
        <form id="assignment-form" action="{{ route('assignments.store') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chọn Giảng viên và Lớp học phần</h3>
                </div>

                <div class="card-body">
                    {{-- Thông báo lỗi --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Chọn giảng viên --}}
                    <div class="form-group">
                        <label for="teacher_id">Chọn Giảng viên</label>
                        <select name="teacher_id" id="teacher_id" class="form-control @error('teacher_id') is-invalid @enderror" required>
                            <option value="">-- Vui lòng chọn một giảng viên --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <hr>
                    <h5>Chọn các Lớp học phần cần phân công</h5>

                    {{-- Danh sách lớp học phần --}}
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 10px;">
                                        <input type="checkbox" id="select-all">
                                    </th>
                                    <th>Mã Lớp</th>
                                    <th>Học phần</th>
                                    <th>Học kỳ</th>
                                    <th>Sĩ số</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($unassignedClasses as $class)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="course_class_ids[]" class="class-checkbox" value="{{ $class->id }}">
                                    </td>
                                    <td>{{ $class->class_code }}</td>
                                    <td>{{ $class->course->name }}</td>
                                    <td>{{ $class->term->name }}</td>
                                    <td>{{ $class->number_of_students }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Không tìm thấy lớp nào chưa được phân công.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 d-flex justify-content-end">
                        {{ $unassignedClasses->links() }}
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Lưu Phân công
                    </button>
                    <a href="{{ route('assignments.index') }}" class="btn btn-secondary">Hủy</a>
                </div>
            </div>
        </form>

    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function () {
        // Chọn hoặc bỏ tất cả checkbox lớp
        $('#select-all').on('click', function () {
            $('.class-checkbox').prop('checked', $(this).prop('checked'));
        });

        // Bỏ check "chọn tất cả" nếu một checkbox con bị bỏ
        $('.class-checkbox').on('change', function () {
            if (!$(this).prop('checked')) {
                $('#select-all').prop('checked', false);
            }
        });
    });
</script>
@stop
