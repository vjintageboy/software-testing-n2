@extends('adminlte::page')

@section('title', 'Chi tiết lương khoa ' . $faculty->name)

@section('content_header')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.reports.faculty_salary', ['year' => $selectedYear]) }}">Báo cáo lương theo khoa</a></li>
        <li class="breadcrumb-item active">Chi tiết khoa {{ $faculty->name }}</li>
    </ol>
    <h1>Chi tiết lương khoa {{ $faculty->name }} - Năm {{ $selectedYear }}</h1>
@stop

@section('content')
    {{-- Thống kê tổng quan --}}
    <div class="row">
        <div class="col-md-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($totalSalary) }}</h3>
                    <p>Tổng lương khoa</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($averageSalary) }}</h3>
                    <p>Lương trung bình/GV</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $teachersCount }}</h3>
                    <p>Số lượng giáo viên</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format($highestSalary) }}</h3>
                    <p>Lương cao nhất</p>
                </div>
                <div class="icon">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Biểu đồ phân bố lương --}}
    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-bar"></i> Phân bố lương giáo viên
            </h3>
        </div>
        <div class="card-body">
            <div style="height: 350px;">
                <canvas id="salaryDistributionChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Bảng chi tiết lương giáo viên --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-table"></i> Danh sách chi tiết lương giáo viên
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Mã GV</th>
                            <th>Họ và tên</th>
                            <th class="text-right">Tổng lương</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teachers as $teacher)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $teacher->teacher_code }}</td>
                                <td>{{ $teacher->full_name }}</td>
                                <td class="text-right">{{ number_format($teacher->total_salary) }} VNĐ</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.reports.teacher_salary_detail', ['teacher_id' => $teacher->id, 'year' => $selectedYear]) }}" 
                                       class="btn btn-info btn-sm">
                                        <i class="fas fa-search"></i> Xem chi tiết
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td colspan="3" class="text-right">Tổng cộng:</td>
                            <td class="text-right">{{ number_format($totalSalary) }} VNĐ</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(function () {
            const ctx = document.getElementById('salaryDistributionChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($distributionLabels) !!},
                    datasets: [{
                        label: 'Số lượng giáo viên',
                        data: {!! json_encode($distributionData) !!},
                        backgroundColor: 'rgba(0, 123, 255, 0.7)',
                        borderColor: 'rgba(0, 123, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Số GV: ' + context.raw;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@stop 