@extends('adminlte::page')

@section('title', 'Báo cáo lương giáo viên')

@section('content_header')
    <h1>Báo cáo tiền dạy của giáo viên theo năm</h1>
@stop

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Chọn năm</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.reports.teacher_salary') }}">
                @csrf
                <div class="row align-items-end">
                    <div class="col-md-10">
                        <div class="form-group">
                            <label for="year">Chọn năm:</label>
                            <select name="year" id="year" class="form-control" required>
                                <option value="">-- Chọn năm --</option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                        Năm {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary form-control">Xem báo cáo</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(isset($reportData))
        @if($reportData->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Danh sách giáo viên năm {{ $selectedYear }}</h3>
                    <div class="card-tools">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <select id="facultyFilter" class="form-control">
                                        <option value="">Tất cả các khoa</option>
                                        @foreach($faculties as $faculty)
                                            <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" id="teacherSearchInput" class="form-control" placeholder="Tìm kiếm theo tên, mã GV...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Tên giáo viên</th>
                                <th>Mã giáo viên</th>
                                <th>Khoa</th>
                                <th>Học vị</th>
                                <th class="text-right">Tổng lương năm</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="teacherTableBody">
                            @foreach($reportData as $teacher)
                                <tr data-faculty-id="{{ $teacher->faculty_id }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $teacher->full_name }}</td>
                                    <td>{{ $teacher->teacher_code }}</td>
                                    <td>{{ $teacher->faculty->name }}</td>
                                    <td>{{ $teacher->degree->name }}</td>
                                    <td class="text-right">{{ number_format($teacher->total_amount) }} VNĐ</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.reports.teacher_salary_detail', ['teacher_id' => $teacher->id, 'year' => $selectedYear]) }}" 
                                           class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Xem chi tiết
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-right">Tổng cộng:</th>
                                <th class="text-right">{{ number_format($totalYearlySalary) }} VNĐ</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @else
            <div class="alert alert-warning">
                Không có dữ liệu lương cho năm <strong>{{ $selectedYear }}</strong>.
            </div>
        @endif
    @endif
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Hàm tổng hợp để áp dụng tất cả bộ lọc
        function applyFilters() {
            var selectedFacultyId = $('#facultyFilter').val();
            var searchTerm = $('#teacherSearchInput').val().toLowerCase();

            $('#teacherTableBody tr').each(function() {
                var row = $(this);
                
                // 1. Kiểm tra lọc theo Khoa
                var facultyId = row.data('faculty-id');
                var facultyMatch = (selectedFacultyId === "" || facultyId == selectedFacultyId);

                // 2. Kiểm tra lọc theo Từ khóa tìm kiếm
                var rowText = row.text().toLowerCase();
                var searchMatch = (rowText.indexOf(searchTerm) > -1);

                // Ẩn/hiện dòng nếu thỏa mãn CẢ HAI điều kiện
                if (facultyMatch && searchMatch) {
                    row.show();
                } else {
                    row.hide();
                }
            });
        }

        // Gán sự kiện cho cả hai ô lọc
        $('#facultyFilter').on('change', applyFilters);
        $('#teacherSearchInput').on('keyup', applyFilters);
    });
</script>
@stop 