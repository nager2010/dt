<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير الرخصة</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            direction: rtl;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }
        .container {
            width: 80%;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
            color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px 15px;
            text-align: right;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #0056b3;
            color: #fff;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f4f4f4;
        }
        tr:hover {
            background-color: #e9f7ff;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
        .highlight {
            font-weight: bold;
            color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>تقرير الرخصة</h1>
    <table>
        <tr>
            <th>الاسم الرباعي</th>
            <td>{{ $record->fullName }}</td>
        </tr>
        <tr>
            <th>اسم المشروع</th>
            <td>{{ $record->projectName }}</td>
        </tr>
        <tr>
            <th>مدة الترخيص</th>
            <td class="highlight">{{ $record->licenseDuration }} سنة</td>
        </tr>
        <tr>
            <th>رسوم الترخيص</th>
            <td>{{ number_format($record->licenseFee) }} د.ل</td>
        </tr>
        <tr>
            <th>تاريخ الانتهاء</th>
            <td class="highlight">{{ $record->endDate ? $record->endDate->format('Y-m-d') : 'غير محدد' }}</td>
        </tr>
    </table>
    <div class="footer">
        <p>تم إنشاء هذا التقرير بواسطة نظام إدارة التراخيص - بلدية سبها</p>
        <p>&copy; 2024 جميع الحقوق محفوظة</p>
    </div>
</div>
</body>
</html>
