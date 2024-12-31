<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>تقرير الرخص</title>
    <style>
        body {
            font-family: dejavusans, sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .report-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .date {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .summary {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="report-title">تقرير الرخص</div>
        <div class="date">تاريخ التقرير: {{ now()->format('Y-m-d') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>رقم الرخصة</th>
                <th>الاسم</th>
                <th>نوع الرخصة</th>
                <th>تاريخ الإصدار</th>
                <th>تاريخ الانتهاء</th>
                <th>الحالة</th>
            </tr>
        </thead>
        <tbody>
            @foreach($licenses as $index => $license)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $license->licenseNumber }}</td>
                <td>{{ $license->fullName }}</td>
                <td>{{ $license->license_type_id }}</td>
                <td>{{ $license->startDate ? \Carbon\Carbon::parse($license->startDate)->format('Y-m-d') : '-' }}</td>
                <td>{{ $license->endDate ? \Carbon\Carbon::parse($license->endDate)->format('Y-m-d') : '-' }}</td>
                <td>
                    @if($license->endDate && \Carbon\Carbon::parse($license->endDate)->isPast())
                        منتهية
                    @else
                        سارية
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <p><strong>إجمالي الرخص:</strong> {{ count($licenses) }}</p>
        <p><strong>الرخص السارية:</strong> 
            {{ $licenses->filter(function($license) {
                return !$license->endDate || !\Carbon\Carbon::parse($license->endDate)->isPast();
            })->count() }}
        </p>
        <p><strong>الرخص المنتهية:</strong> 
            {{ $licenses->filter(function($license) {
                return $license->endDate && \Carbon\Carbon::parse($license->endDate)->isPast();
            })->count() }}
        </p>
    </div>
</body>
</html>
