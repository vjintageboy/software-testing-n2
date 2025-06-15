@extends('adminlte::page')

@section('title', 'Báo cáo lương theo khoa')

@section('content_header')
    <h1>Báo cáo lương theo khoa</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.reports.faculty_salary') }}">
            @csrf
            <div class="row align-items-end">
                <div class="col-md-10">
                    <div class="form-group">
                        <label for="year">Chọn năm:</label>
                        <select name="year" id="year" class="form-control">
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
                        <button type="submit" class="btn btn-primary form-control">
                            <i class="fas fa-search"></i> Xem báo cáo
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@if(isset($reportData))
    @if($reportData->count() > 0)
        {{-- Biểu đồ tổng quan --}}
        <div class="row">
            <div class="col-md-6">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie"></i> Phân bố lương theo khoa - Năm {{ $selectedYear }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div style="height: 350px;">
                            <canvas id="facultySalaryPieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar"></i> Top 5 khoa có lương cao nhất - Năm {{ $selectedYear }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div style="height: 350px;">
                            <canvas id="facultySalaryBarChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bảng thống kê chi tiết --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-table"></i> Thống kê chi tiết lương theo khoa - Năm {{ $selectedYear }}
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Tên Khoa</th>
                                <th class="text-center">Số lượng GV</th>
                                <th class="text-right">Tổng lương</th>
                                <th class="text-right">Lương trung bình/GV</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportData as $faculty)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $faculty->name }}</td>
                                    <td class="text-center">{{ $faculty->teachers_count }}</td>
                                    <td class="text-right">{{ number_format($faculty->total_salary) }} VNĐ</td>
                                    <td class="text-right">{{ number_format($faculty->average_salary) }} VNĐ</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.reports.faculty_salary_detail', ['faculty' => $faculty->id, 'year' => $selectedYear]) }}" 
                                           class="btn btn-primary btn-sm">
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
                                <td class="text-right">{{ number_format($averageSalary) }} VNĐ</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> Không có dữ liệu lương cho năm {{ $selectedYear }}.
        </div>
    @endif
@endif
@stop

@section('js')
@if(isset($reportData) && $reportData->count() > 0)
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(function () {
            // Biểu đồ tròn
            const pieCtx = document.getElementById('facultySalaryPieChart').getContext('2d');
            new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        data: {!! json_encode($chartData) !!},
                        backgroundColor: [
                            '#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc',
                            '#d2d6de', '#6f42c1', '#20c997', '#fd7e14', '#17a2b8'
                        ]
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.raw || 0;
                                    return label + ': ' + new Intl.NumberFormat('vi-VN', { 
                                        style: 'currency', 
                                        currency: 'VND' 
                                    }).format(value);
                                }
                            }
                        }
                    }
                }
            });

            // Biểu đồ cột
            const barCtx = document.getElementById('facultySalaryBarChart').getContext('2d');
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($top5Labels) !!},
                    datasets: [{
                        label: 'Tổng lương',
                        data: {!! json_encode($top5Data) !!},
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
                                callback: function(value) {
                                    return new Intl.NumberFormat('vi-VN', { 
                                        style: 'currency', 
                                        currency: 'VND',
                                        notation: 'compact'
                                    }).format(value);
                                }
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
                                    let value = context.raw || 0;
                                    return 'Tổng lương: ' + new Intl.NumberFormat('vi-VN', { 
                                        style: 'currency', 
                                        currency: 'VND' 
                                    }).format(value);
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endif
@stop 