@extends('adminlte::page')

@section('title', 'Thống kê Học phần')

@section('content_header')
    <h1 class="m-0 text-dark">Thống kê và Báo cáo Học phần</h1>
@stop

@section('content')
    {{-- Form lọc --}}
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Bộ lọc" theme="info" icon="fas fa-filter" collapsible>
                <form method="GET" action="{{ route('admin.statistics.courses') }}">
    <div class="row">
        <div class="col-md-4">
            <x-adminlte-select name="term_id" label="Chọn Học Kỳ">
                @if($terms->isEmpty())
                    <option>Chưa có học kỳ nào</option>
                @else
                    {{-- Option: Tất cả học kỳ --}}
                    <option value="" {{ (string)$selectedTermId === '' ? 'selected' : '' }}>
                        Tất cả học kỳ
                    </option>

                    {{-- Option: Danh sách học kỳ --}}
                    @foreach($terms as $term)
                        <option value="{{ $term->id }}" {{ (string)$term->id === (string)$selectedTermId ? 'selected' : '' }}>
                            {{ $term->name }}
                        </option>
                    @endforeach
                @endif
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

    {{-- Các thẻ thông tin --}}
    <div class="row">
        <div class="col-md-4">
            <x-adminlte-info-box title="Tổng số Học phần" text="{{ $widgetData['totalUniqueCourses'] }}" icon="fas fa-book-open text-white" theme="gradient-primary"/>
        </div>
        <div class="col-md-4">
            <x-adminlte-info-box title="Tổng Lớp mở (trong kỳ)" text="{{ $widgetData['totalClassesInTerm'] }}" icon="fas fa-chalkboard text-info text-white" theme="gradient-info"/>
        </div>
        <div class="col-md-4">
             <x-adminlte-info-box title="Tổng SV đăng ký (trong kỳ)" text="{{ number_format($widgetData['totalStudentsInTerm']) }}" icon="fas fa-user-graduate text-success text-white" theme="gradient-success"/>
        </div>
    </div>

    {{-- Biểu đồ --}}
    <div class="row">
        <div class="col-md-6">
            <x-adminlte-card title="Phân bố Học phần theo Khoa" theme="lightblue" theme-mode="outline"
                icon="fas fa-university" header-class="text-bold" collapsible>
                <canvas id="facultyChart" style="height: 300px;"></canvas>
            </x-adminlte-card>
        </div>
        <div class="col-md-6">
            <x-adminlte-card title="Phân bố Học phần theo Tín chỉ" theme="purple" theme-mode="outline"
                icon="fas fa-star" header-class="text-bold" collapsible>
                 <canvas id="creditsChart" style="height: 300px;"></canvas>
            </x-adminlte-card>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <x-adminlte-card title="Top 15 Học phần có nhiều sinh viên đăng ký nhất (Kỳ: {{ $widgetData['activeTermName'] }})" theme="orange" theme-mode="outline"
                icon="fas fa-chart-line" header-class="text-bold" collapsible>
                 <canvas id="enrollmentChart" style="height: 400px;"></canvas>
            </x-adminlte-card>
        </div>
    </div>

    {{-- Bảng dữ liệu chi tiết --}}
    <div class="row">
        <div class="col-12">
             @php
                $heads = [
                    ['label' => 'STT', 'width' => 5],
                    'Mã HP',
                    ['label' => 'Tên Học phần', 'width' => 40],
                    'Khoa phụ trách',
                    ['label' => 'Số lớp mở', 'width' => 10],
                    ['label' => 'Tổng SV', 'width' => 10],
                ];
                $config = [
                    'data' => $courseTermStats->map(function ($item, $key) {
                        return [
                            $key + 1,
                            $item['course_code'],
                            $item['course_name'],
                            $item['faculty_name'],
                            $item['class_count'],
                            $item['total_students'],
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

            <x-adminlte-card title="Bảng thống kê chi tiết học phần (Kỳ: {{ $widgetData['activeTermName'] }})"
                icon="fas fa-list-alt" theme="success" theme-mode="outline" collapsible>
                <x-adminlte-datatable id="courseStatsTable" :heads="$heads" :config="$config" striped hoverable with-buttons/>
            </x-adminlte-card>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Biểu đồ Học phần theo Khoa
            new Chart(document.getElementById('facultyChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($facultyChartData['labels']),
                    datasets: [{
                        label: 'Số lượng Học phần',
                        data: @json($facultyChartData['data']),
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                }
            });

            // Biểu đồ Học phần theo Tín chỉ
            new Chart(document.getElementById('creditsChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: @json($creditsChartData['labels']),
                    datasets: [{
                        data: @json($creditsChartData['data']),
                        backgroundColor: ['rgba(75, 192, 192, 0.7)', 'rgba(153, 102, 255, 0.7)', 'rgba(255, 159, 64, 0.7)', 'rgba(255, 99, 132, 0.7)', 'rgba(54, 162, 235, 0.7)'],
                        borderWidth: 1
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            // Biểu đồ Top học phần theo số lượng SV
            new Chart(document.getElementById('enrollmentChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($enrollmentChartData['labels']),
                    datasets: [{
                        label: 'Số sinh viên đăng ký',
                        data: @json($enrollmentChartData['data']),
                        backgroundColor: 'rgba(255, 159, 64, 0.6)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { x: { beginAtZero: true } }
                }
            });
        });
    </script>
@stop