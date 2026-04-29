<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>كشف مسير الرواتب المجمع</title>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
        }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 20px; background: #f9f9f9; direction: rtl; text-align: right; }
        .container { max-width: 100%; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 13px; }
        th, td { border: 1px solid #000; padding: 10px; text-align: center; }
        th { background-color: #e5e7eb; font-weight: bold; }
        .text-center { text-align: center; }
        .header { margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .btn-print { background: #10b981; color: white; border: none; padding: 10px 20px; font-size: 16px; cursor: pointer; border-radius: 5px; margin-bottom: 20px; display: inline-flex; align-items: center; gap: 8px; }
        .btn-print:hover { background: #059669; }
    </style>
</head>
<body>
    <div class="text-center no-print">
        <button class="btn-print" onclick="window.print()">🖨️ طباعة كشف الرواتب المجمع (PDF)</button>
    </div>

    <div class="container">
        <div class="header text-center">
            <h2>كشف مسير الرواتب المجمع (Payroll Summary)</h2>
            <p>لشهر: {{ $filterData['month'] ?? date('m') }} / {{ $filterData['year'] ?? date('Y') }}</p>
        </div>

        @if(count($bulkPayrollData) > 0)
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم الموظف</th>
                        <th>الراتب الأساسي</th>
                        <th>البدلات (+)</th>
                        <th>الإضافي (+)</th>
                        <th>التأخيرات (-)</th>
                        <th>الغياب (-)</th>
                        <th>السلف (-)</th>
                        <th>التأمينات (-)</th>
                        <th>صافي الراتب</th>
                        <th>التوقيع</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalNet = 0; $totalBasic = 0; @endphp
                    @foreach($bulkPayrollData as $key => $data)
                        @php 
                            $totalNet += $data['net_salary']; 
                            $totalBasic += $data['basic_salary'];
                        @endphp
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td style="text-align: right; font-weight: bold;">{{ $data['name'] }}</td>
                            <td>{{ number_format($data['basic_salary'], 2) }}</td>
                            <td>{{ number_format($data['allowance'], 2) }}</td>
                            <td style="color: green;">{{ number_format($data['overtime'], 2) }}</td>
                            <td style="color: red;">{{ number_format($data['undertime'], 2) }}</td>
                            <td style="color: red;">{{ number_format($data['absent'], 2) }}</td>
                            <td style="color: red;">{{ number_format($data['advance'], 2) }}</td>
                            <td style="color: red;">{{ number_format($data['ssf'], 2) }}</td>
                            <td style="font-weight: bold; background: #f3f4f6;">{{ number_format($data['net_salary'], 2) }} {{ $currency }}</td>
                            <td></td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: #e5e7eb; font-weight: bold;">
                        <td colspan="2">الإجمالي الكلي</td>
                        <td>{{ number_format($totalBasic, 2) }}</td>
                        <td colspan="6"></td>
                        <td style="color: green; font-size: 16px;">{{ number_format($totalNet, 2) }} {{ $currency }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        @else
            <h3 class="text-center" style="color: red; margin-top: 50px;">لا توجد رواتب مصدرة في هذا الشهر</h3>
        @endif
    </div>
</body>
</html>