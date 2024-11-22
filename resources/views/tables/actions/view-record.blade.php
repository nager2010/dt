<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل السجل</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            direction: rtl;
            text-align: right;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .details {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
@php
    // تعطيل الخطوات الإضافية
    $disableSteps = true;
@endphp

<div class="header">
    <h2>تفاصيل السجل</h2>
</div>
<div class="details">
    <p><strong>الاسم الكامل:</strong> {{ $record['fullName'] }}</p>
    <p><strong>الرقم الوطني:</strong> {{ $record['nationalID'] }}</p>
    <p><strong>رقم الهاتف:</strong> {{ $record['phoneNumber'] }}</p>
    <p><strong>اسم المشروع:</strong> {{ $record['projectName'] }}</p>
    <p><strong>عنوان المشروع:</strong> {{ $record['projectAddress'] ?? 'غير متوفر' }}</p>
    <p><strong>البلدية:</strong> {{ $record['municipality'] ?? 'غير متوفرة' }}</p>
    <p><strong>الأيام المتبقية:</strong>
        @if($record['endDate'])
            {{ \Carbon\Carbon::parse($record['endDate'])->diffInDays(now(), false) }} يوم
        @else
            غير محدد
        @endif
    </p>
</div>
</body>
</html>
