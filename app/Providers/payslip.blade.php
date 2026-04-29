<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إشعار قسيمة الراتب</title>
</head>
<body style="font-family: Tahoma, sans-serif; direction: rtl; text-align: right;">
    <h2>مرحباً {{ $payrollData->employee->name }},</h2>
    <p>تجدون مرفقاً قسيمة الراتب الخاصة بكم لشهر {{ $payrollData->payslip_month }}/{{ $payrollData->payslip_year }}.</p>
    <p>هذه الرسالة تم إنشاؤها تلقائياً من نظام الموارد البشرية.</p>
    <br>
    <p>مع تحيات،</p>
    <p>إدارة الموارد البشرية</p>
</body>
</html>