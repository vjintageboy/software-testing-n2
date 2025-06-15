<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bảng kê lương - {{ $selectedTeacher->full_name }} - Năm {{ $year }}</title>
    <style>
        body {
            font-family: 'dejavu sans', sans-serif; /* Font hỗ trợ tiếng Việt */
            font-size: 12px;
            color: #333;
        }
        .container { width: 100%; margin: 0 auto; }
        .header, .footer { text-align: center; }
        .header h1 { margin: 0; font-size: 18px; }
        .header h2 { margin: 0; font-size: 16px; font-weight: bold; }
        .header p { margin: 0; font-size: 13px; }
        .invoice-details { margin: 20px 0; }
        .invoice-details table { width: 100%; border: none; }
        .invoice-details td { padding: 3px 0; }
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { border: 1px solid #999; padding: 6px; text-align: left; }
        .data-table th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-year { font-weight: bold; font-size: 1.3em; margin-top: 20px; }
        .signatures { margin-top: 60px; }
        .signatures table { width: 100%; border: none; }
        .signatures td { border: none; text-align: center; width: 33.33%; }
        .signatures .signature-line { margin-top: 60px; }
        .term-name { font-size: 14px; font-weight: bold; padding: 8px; background-color: #e9ecef; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>TRƯỜNG ĐẠI HỌC ABC</h2>
            <p><strong>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM<br>Độc lập - Tự do - Hạnh phúc</strong></p>
            <hr>
            <h1>BẢNG KÊ THANH TOÁN TIỀN GIẢNG DẠY</h1>
            <p>Năm học: {{ $year }}</p>
        </div>

        <div class="invoice-details">
            <table>
                <tr>
                    <td style="width: 50%;"><strong>Giáo viên:</strong> {{ $selectedTeacher->full_name }}</td>
                    <td style="width: 50%;"><strong>Mã giáo viên:</strong> {{ $selectedTeacher->teacher_code }}</td>
                </tr>
                <tr>
                    <td><strong>Khoa:</strong> {{ $selectedTeacher->faculty->name }}</td>
                    <td><strong>Học vị:</strong> {{ $selectedTeacher->degree->name }} (Hệ số: {{ $selectedTeacher->degree->coefficient }})</td>
                </tr>
                 <tr>
                    <td colspan="2"><strong>Ngày xuất bảng kê:</strong> {{ date('d/m/Y') }}</td>
                </tr>
            </table>
        </div>

        @foreach($reportDetails as $termReport)
            <table class="data-table">
                <thead>
                    <tr>
                        <th colspan="7" class="term-name">{{ $termReport['term_name'] }}</th>
                    </tr>
                    <tr>
                        <th>Môn học (Lớp)</th>
                        <th>Sĩ số</th>
                        <th>Số tiết chuẩn</th>
                        <th>Đơn giá tiết</th>
                        <th>HS Học vị</th>
                        <th>HS Học phần</th>
                        <th>HS Sĩ số</th>
                        <th>Số tiết quy đổi</th>
                        <th class="text-right">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($termReport['details'] as $detail)
                        <tr>
                            <td>{{ $detail['course_name'] }} ({{ $detail['class_code'] }})</td>
                            <td class="text-center">{{ $detail['number_of_students'] }}</td>
                            <td class="text-center">{{ $detail['standard_periods'] }}</td>
                            <td class="text-right">{{ number_format($detail['base_pay_per_period']) }}</td>
                            <td class="text-center">{{ $detail['degree_coefficient'] }}</td>
                            <td class="text-center">{{ $detail['course_coefficient'] }}</td>
                            <td class="text-center">{{ $detail['class_size_coefficient'] }}</td>
                            <td class="text-center">{{ $detail['converted_periods'] }}</td>
                            <td class="text-right">{{ number_format($detail['class_amount']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6" class="text-right">Tổng cộng học kỳ:</th>
                        <th class="text-right">{{ number_format($termReport['term_total']) }} VNĐ</th>
                    </tr>
                </tfoot>
            </table>
        @endforeach

        <div class="text-right total-year">
            <p>TỔNG CỘNG CẢ NĂM: {{ number_format($totalYearlySalary) }} VNĐ</p>
        </div>

        <div class="signatures">
            <table>
                <tr>
                    <td><strong>Người lập bảng</strong><br>(Ký, họ tên)</td>
                    <td><strong>Phòng Kế toán</strong><br>(Ký, họ tên)</td>
                    <td><strong>Ban Giám hiệu</strong><br>(Ký, họ tên, đóng dấu)</td>
                </tr>
                <tr>
                    <td class="signature-line">{{ $selectedTeacher->full_name }}</td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html> 