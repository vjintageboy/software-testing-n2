:@extends('adminlte::page')

@section('title', 'Thống kê Giáo viên')

@section('content_header')
    <h1 class="m-0 text-dark">Thống kê và Báo cáo Giáo viên</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Bộ lọc" theme="info" icon="fas fa-filter" collapsible>
                <form method="GET" action="{{ route('admin.statistics.teachers') }}">
    <div class="row">
        <div class="col-md-4">
            <x-adminlte-select name="term_id" label="Chọn Học Kỳ">
                {{-- Option: Tất cả học kỳ --}}
                <option value="" {{ (string)$selectedTermId === '' ? 'selected' : '' }}>
                    Tất cả học kỳ
                </option>

                {{-- Option: Các học kỳ có trong danh sách --}}
                @foreach($terms as $term)
                    <option value="{{ $term->id }}" {{ (string)$term->id === (string)$selectedTermId ? 'selected' : '' }}>
                        {{ $term->name }}
                    </option>
                @endforeach
            </x-adminlte-select>
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <x-adminlte-button 
                type="submit"
                label="Xem thống kê"
                theme="primary"
                icon="fas fa-search"
                style="margin-bottom: 5% !important;"
            />
        </div>
    </div>
</form>

            </x-adminlte-card>
        </div>
    </div>

    {{-- Các thẻ thông tin tổng quan --}}
    <div class="row">
        <div class="col-md-4">
            <x-adminlte-info-box title="Tổng số Giáo viên" text="{{ $widgetData['totalTeachers'] }}" icon="fas fa-users text-info text-white" theme="gradient-info"/>
        </div>
        <div class="col-md-4">
            <x-adminlte-info-box title="Học kỳ đang xem" text="{{ $widgetData['activeTermName'] }}" icon="fas fa-calendar-alt text-success text-white" theme="gradient-success"/>
        </div>
        <div class="col-md-4">
             <x-adminlte-info-box title="Tổng giờ quy đổi (trong kỳ)" text="{{ number_format($widgetData['totalHoursThisTerm'], 2) }} giờ" icon="fas fa-clock text-warning text-white" theme="gradient-warning"/>
        </div>
    </div>

    {{-- Biểu đồ --}}
    <div class="row">
        <div class="col-md-6">
            <x-adminlte-card title="Phân bố Giáo viên theo Khoa" theme="lightblue" theme-mode="outline"
                icon="fas fa-university" header-class="text-bold" collapsible>
                <canvas id="facultyChart"></canvas>
            </x-adminlte-card>
        </div>
        <div class="col-md-6">
            <x-adminlte-card title="Phân bố Giáo viên theo Học vị" theme="purple" theme-mode="outline"
                icon="fas fa-graduation-cap" header-class="text-bold" collapsible>
                 <canvas id="degreeChart"></canvas>
            </x-adminlte-card>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Top 15 Giáo viên có giờ dạy cao nhất (Học kỳ: {{ $widgetData['activeTermName'] }})" theme="orange" theme-mode="outline"
                icon="fas fa-chart-line" header-class="text-bold" collapsible>
                 <canvas id="workloadChart" style="height: 400px;"></canvas>
            </x-adminlte-card>
        </div>
    </div>

    {{-- Bảng dữ liệu chi tiết --}}
    <div class="row">
        <div class="col-12">
             @php
                $heads = [
                    ['label' => 'STT', 'width' => 5],
                    ['label' => 'Tên Giáo viên', 'width' => 30],
                    ['label' => 'Khoa'],
                    ['label' => 'Học vị'],
                    ['label' => 'Số lớp dạy', 'width' => 10],
                    ['label' => 'Tổng giờ quy đổi', 'width' => 15],
];
                $config = [
                    'data' => $teacherWorkload->map(function ($item, $key) {
                        return [
                            $key + 1,
                            $item['teacher_name'],
                            $item['faculty_name'],
                            $item['degree_name'],
                            $item['total_classes'],
                            $item['total_hours'],
                        ];
                    })->toArray(),
                    'order' => [[5, 'desc']],
                    'columns' => [['orderable' => false], null, null, null, null, ['orderable' => true]],
                    'pageLength' => 25,
                    'responsive' => true,
                    'autoWidth' => false,
                    'language' => ['url' => '//cdn.datatables.net/plug-ins/1.10.21/i18n/Vietnamese.json'],
                ];
            @endphp

            <x-adminlte-card title="Bảng thống kê khối lượng giảng dạy (Học kỳ: {{ $widgetData['activeTermName'] }})"
                icon="fas fa-clipboard-list" theme="success" theme-mode="outline" collapsible>
                <x-adminlte-datatable id="workloadTable" :heads="$heads" :config="$config" striped hoverable with-buttons/>
            </x-adminlte-card>
        </div>
    </div>
@stop

@section('js')
    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Biểu đồ cột: Phân bố giáo viên theo khoa
            var facultyCtx = document.getElementById('facultyChart').getContext('2d');
            new Chart(facultyCtx, {
                type: 'bar',
                data: {
                    labels: @json($facultyChartData['labels']),
                    datasets: [{
                        label: 'Số lượng Giáo viên',
                        data: @json($facultyChartData['data']),
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });

            // Biểu đồ tròn: Phân bố giáo viên theo học vị
            var degreeCtx = document.getElementById('degreeChart').getContext('2d');
            new Chart(degreeCtx, {
                type: 'pie',
                data: {
                    labels: @json($degreeChartData['labels']),
                    datasets: [{
                        label: 'Tỷ lệ Học vị',
                        data: @json($degreeChartData['data']),
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)', 'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)', 'rgba(75, 192, 192, 0.7)',
                            'rgba(153, 102, 255, 0.7)', 'rgba(255, 159, 64, 0.7)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            // Biểu đồ ngang: Top giáo viên có giờ dạy cao nhất
            var workloadCtx = document.getElementById('workloadChart').getContext('2d');
            new Chart(workloadCtx, {
                type: 'bar',
                data: {
                    labels: @json($workloadChartData['labels']),
                    datasets: [{
                        label: 'Tổng giờ quy đổi',
                        data: @json($workloadChartData['data']),
                        backgroundColor: 'rgba(255, 159, 64, 0.6)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { x: { beginAtZero: true } }
                }
            });
        });
    </script>
@stop