<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>التقرير المالي</title>
    <style>
        body {
            font-family: dejavusans, sans-serif;
            padding: 20px;
            direction: rtl;
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
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="report-title">التقرير المالي</div>
        <div class="date">
            الفترة: 
            @if($period === 'custom')
                من {{ $startDate }} إلى {{ $endDate }}
            @else
                {{ $period }}
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>التاريخ</th>
                <th>عدد الرخص</th>
                <th>إجمالي الرسوم</th>
            </tr>
        </thead>
        <tbody>
            @foreach($revenues as $revenue)
                <tr>
                    <td>{{ $revenue->date }}</td>
                    <td>{{ $revenue->total_licenses }}</td>
                    <td>{{ number_format($revenue->total_fees, 3) }} د.ل</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
