@extends('adminlte::page')

@section('title', 'Chi tiết báo cáo lương giáo viên')

@section('content_header')
    <h1>Chi tiết báo cáo lương giáo viên</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Bảng kê chi tiết lương năm {{ $year }} cho giáo viên: <strong>{{ $selectedTeacher->full_name }}</strong>
            </h3>
            <div class="card-tools">
                <a href="{{ route('admin.reports.teacher_salary.pdf', ['teacher_id' => $selectedTeacher->id, 'year' => $year]) }}" 
                   class="btn btn-danger btn-sm" target="_blank">
                    <i class="fas fa-file-pdf"></i> Xuất Hóa Đơn (PDF)
                </a>
                <a href="{{ route('admin.reports.teacher_salary') }}?year={{ $year }}" 
                   class="btn btn-default btn-sm">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="accordion" id="salaryReportAccordion">
                @foreach($reportDetails as $termReport)
                    <div class="card mb-0">
                        <div class="card-header" id="heading{{ $loop->index }}">
                            <h2 class="mb-0">
                                <button class="btn btn-link btn-block text-left d-flex justify-content-between" 
                                        type="button" 
                                        data-toggle="collapse" 
                                        data-target="#collapse{{ $loop->index }}" 
                                        aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                                    <span class="h5 font-weight-bold">{{ $termReport['term_name'] }}</span>
                                    <span class="h5 font-weight-bold">Tổng cộng: {{ number_format($termReport['term_total']) }} VNĐ</span>
                                </button>
                            </h2>
                        </div>

                        <div id="collapse{{ $loop->index }}" 
                             class="collapse {{ $loop->first ? 'show' : '' }}" 
                             data-parent="#salaryReportAccordion">
                            <div class="card-body">
                                <p class="font-italic">Công thức tính: Thành tiền = (Số tiết chuẩn) * (HS học phần + HS sĩ số) * (HS học vị) * (Đơn giá tiết)</p>
                                <table class="table table-hover table-sm table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Môn học (Lớp)</th>
                                            <th>Sĩ số</th>
                                            <th>Số tiết chuẩn</th>
                                            <th>Đơn giá tiết (VNĐ)</th>
                                            <th>HS Học vị</th>
                                            <th>HS Học phần</th>
                                            <th>HS Sĩ số</th>
                                            <th>Số tiết quy đổi</th>
                                            <th class="text-right">Thành tiền (VNĐ)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($termReport['details'] as $detail)
                                            <tr>
                                                <td>{{ $detail['course_name'] }} ({{ $detail['class_code'] }})</td>
                                                <td>{{ $detail['number_of_students'] }}</td>
                                                <td>{{ $detail['standard_periods'] }}</td>
                                                <td>{{ number_format($detail['base_pay_per_period']) }}</td>
                                                <td>{{ $detail['degree_coefficient'] }}</td>
                                                <td>{{ $detail['course_coefficient'] }}</td>
                                                <td>{{ $detail['class_size_coefficient'] }}</td>
                                                <td>{{ $detail['converted_periods'] }}</td>
                                                <td class="text-right">{{ number_format($detail['class_amount']) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="card-footer text-right">
            <h4 class="font-weight-bold">TỔNG LƯƠNG CẢ NĂM: {{ number_format($totalYearlySalary) }} VNĐ</h4>
        </div>
    </div>
@stop 