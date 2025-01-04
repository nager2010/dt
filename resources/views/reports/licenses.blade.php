<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير الرخص</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            direction: rtl;
            padding: 20px;
            font-size: 14px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .filters {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        .filter-item {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status-active {
            color: green;
        }
        .status-expired {
            color: red;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>تقرير الرخص</h2>
    </div>

    <div class="filters">
        <div class="filter-item">
            <strong>الفترة:</strong> من {{ $startDate }} إلى {{ $endDate }}
        </div>
        <div class="filter-item">
            <strong>البلدية:</strong> {{ $municipality }}
        </div>
        <div class="filter-item">
            <strong>المنطقة:</strong> {{ $region }}
        </div>
        <div class="filter-item">
            <strong>حالة الرخصة:</strong>
            @switch($status)
                @case('active')
                    سارية
                    @break
                @case('expired')
                    منتهية
                    @break
                @default
                    الكل
            @endswitch
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>رقم الرخصة</th>
                <th>اسم المشروع</th>
                <th>البلدية</th>
                <th>المنطقة</th>
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
                    <td>{{ $license->projectName }}</td>
                    <td>{{ $license->municipality->name }}</td>
                    <td>{{ $license->region->name }}</td>
                    <td>{{ $license->licenseDate }}</td>
                    <td>{{ $license->endDate }}</td>
                    <td class="{{ $license->endDate >= now() ? 'status-active' : 'status-expired' }}">
                        {{ $license->endDate >= now() ? 'سارية' : 'منتهية' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>تم إنشاء هذا التقرير في {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>إجمالي عدد الرخص: {{ $licenses->count() }}</p>
    </div>
</body>
</html>
