<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ __('index.clearance_form') }} - {{ $user->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; padding: 40px; color: #333; background: #fff; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #8a0c51; padding-bottom: 15px; }
        .title { color: #8a0c51; font-weight: 700; font-size: 28px; margin: 0; }
        .subtitle { color: #64748b; font-size: 14px; margin-top: 5px; }
        .section-title { background-color: #f8fafc; padding: 10px 15px; border-radius: 5px; font-weight: 600; margin-top: 30px; border-right: 4px solid #8a0c51; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 14px; }
        table, th, td { border: 1px solid #e2e8f0; }
        th, td { padding: 12px; text-align: right; }
        th { background-color: #f1f5f9; color: #475569; width: 20%; }
        td { width: 30%; color: #1e293b; font-weight: 600; }
        .signatures { display: flex; justify-content: space-between; margin-top: 60px; }
        .signature-box { text-align: center; width: 22%; border-top: 1px solid #cbd5e1; padding-top: 10px; color: #475569; font-weight: 600; }
        .print-btn { padding: 10px 25px; background: #8a0c51; color: white; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo'; font-weight: 600; font-size: 16px; margin-bottom: 20px; transition: 0.3s; }
        .print-btn:hover { background: #6a093e; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            .section-title { border-right-color: #000 !important; background-color: #eee !important; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: left;">
        <button onclick="window.print()" class="print-btn">🖨️ طباعة النموذج</button>
        <button onclick="window.close()" class="print-btn" style="background: #64748b; margin-right: 10px;">إغلاق</button>
    </div>

    <div class="header">
        <h1 class="title">نموذج إخلاء طرف</h1>
        <div class="subtitle">Employee Clearance Form</div>
        <p style="margin-top: 10px; font-weight: 600;">تاريخ الإصدار: {{ date('Y-m-d') }}</p>
    </div>

    <div class="section-title">👤 بيانات الموظف (Employee Details)</div>
    <table>
        <tr>
            <th>اسم الموظف</th>
            <td>{{ $user->name }}</td>
            <th>الرقم الوظيفي</th>
            <td>{{ $user->employee_code ?? 'غير مسجل' }}</td>
        </tr>
        <tr>
            <th>القسم</th>
            <td>{{ $user->department->dept_name ?? 'غير مسجل' }}</td>
            <th>المسمى الوظيفي</th>
            <td>{{ $user->post->post_name ?? 'غير مسجل' }}</td>
        </tr>
        <tr>
            <th>تاريخ الانضمام</th>
            <td>{{ $user->joining_date ?? 'غير مسجل' }}</td>
            <th>تاريخ إخلاء الطرف</th>
            <td>{{ date('Y-m-d') }}</td>
        </tr>
    </table>

    <div class="section-title">💻 العهد والأصول المسلمة (Assigned Assets)</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">م</th>
                <th style="width: 35%;">اسم العهدة / الوصف</th>
                <th style="width: 20%;">الرقم التسلسلي</th>
                <th style="width: 15%;">تاريخ الاستلام</th>
                <th style="width: 25%;">حالة الإرجاع</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $key => $asset)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $asset->name }}</td>
                <td>{{ $asset->asset_serial_no ?? '-' }}</td>
                <td>{{ $asset->assigned_date ?? '-' }}</td>
                <td><input type="checkbox"> مستلمة &nbsp;&nbsp; <input type="checkbox"> مفقودة/تالفة</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; color: #64748b; font-weight: normal;">لا توجد أي عهد أو أصول مسجلة باسم هذا الموظف في النظام.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">📝 إقرار وتسوية (Declaration)</div>
    <p style="margin-top: 15px; line-height: 1.8; color: #333; font-weight: 600; text-align: justify;">
        أقر أنا الموقع أدناه بأنني قمت بتسليم جميع العهد والأصول التابعة للشركة والموضحة أعلاه، وأنه لا توجد في ذمتي أي التزامات مالية أو عينية أو مستندات تخص الشركة. وبناءً عليه أطلب إخلاء طرفي.
    </p>
    
    <div class="signatures">
        <div class="signature-box">توقيع الموظف<br><br><br></div>
        <div class="signature-box">المدير المباشر<br><br><br></div>
        <div class="signature-box">قسم تقنية المعلومات (IT)<br><br><br></div>
        <div class="signature-box">الموارد البشرية (HR)<br><br><br></div>
    </div>
</body>
</html>