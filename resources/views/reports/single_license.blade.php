<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تفاصيل الرخصة</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            line-height: 1.8;
            margin: 0; /* إزالة أي مسافات افتراضية */
            padding: 120px 20px 20px 20px; /* إضافة 120 بكسل من الأعلى و20 من الجوانب */
        }
        .inline {
            display: inline-block;
            margin-right: 20px;
        }
        .center-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #555; /* لون رمادي */
            background-color: #f0f0f0; /* خلفية رمادية */
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .footer {
            text-align: left;
            margin-top: 50px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .table td, .table th {
            border: 1px solid black;
            padding: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
<div>
    <div>
        <p>
            <br><br><br><br><br>
            <span class="inline"><strong>نوع الترخيص:</strong>
    {{
        match($record->license_type_id) {
            'commercial' => 'تجاري',
            'industrial' => 'صناعي',
            'craft_service' => 'حرفي خدمي',
            'professional_service' => 'خدمي مهني',
            'general' => 'عام',
            'street_vendor' => 'بائع متجول',
            'holding_company' => 'شركة قابضة',
            default => 'غير محدد',
        }
    }}
</span>

            <span class="inline"><strong>رقم الترخيص:</strong> {{ $record->licenseNumber }}</span>
            <span class="inline"><strong>التاريخ:</strong> {{ \Carbon\Carbon::parse($record->licenseDate)->format('Y-m-d') }}</span>
        </p>
        <p>
            <span class="inline"><strong>بعد الاطلاع على الطلب المقدم من السيد / ة:</strong> {{ $record->fullName }}</span>
        </p>
        <p>
            <span class="inline"><strong>رقم جواز سفر أو بطاقة شخصية:</strong> {{ $record->passportOrID }}</span>
            <span class="inline"><strong>بصفته:</strong> {{ $record->positionInProject }}</span>
        </p>
        <p class="center-title">يرخص</p>

        <p>
            <span class="inline"><strong>للسيد/ة (الاسم الرباعي):</strong> {{ $record->fullName }}</span>
        </p>
        <p>
            <span class="inline"><strong>الرقم الضريبي:</strong> {{ $record->taxNumber }}</span>
            <span class="inline"><strong>سجل تجاري رقم:</strong> {{ $record->economicNumber }}</span>
        </p>
        <p>
            <span class="inline"><strong>بمزاولة نشاط:</strong> {{ $record->nearestLandmark }}</span>
            <span class="inline">
    <strong>بمقرها الكائن:</strong>
    {{ $record->municipality->name ?? 'غير محدد' }} - {{ $record->region->name ?? 'غير محدد' }}
</span>

        </p>
        <p>
            <span class="inline"><strong>الاسم التجاري:</strong> {{ $record->projectName }}</span>
            <span class="inline"><strong>البريد الإلكتروني:</strong> {{ $record->email }}</span>
        </p>
        <table class="table">
            <tr>
                @foreach(str_split($record->registrationCode) as $digit)
                    <td>{{ $digit }}</td>
                @endforeach
            </tr>
        </table>
        <p>
            <span class="inline"><strong>هذه الرخصة صالحة إلى:</strong> {{ \Carbon\Carbon::parse($record->endDate)->format('Y-m-d') }}</span>
            <span class="inline"><strong>المدة بالحروف:</strong> {{ $modah }} من تاريخ الإصدار</span>
        </p>
        <p>
            <span class="inline"><strong>سددت الرسوم بواقع:</strong> {{ $record->discount }} د.ل</span>
            <span class="inline"><strong>بالحروف:</strong> {{ $ejmalybelhroof }}</span>
        </p>
        <p>
            <span class="inline"><strong>بموجب إيصال مالي:</strong> {{ $record->chamberOfCommerceNumber }}</span>
            <span class="inline"><strong>بتاريخ:</strong> {{ \Carbon\Carbon::parse($record->licenseDate)->format('Y-m-d') }}</span>
        </p>
        <p>
            <span class="inline"><strong>صدرت بتاريخ:</strong> {{ \Carbon\Carbon::parse($record->licenseDate)->format('Y-m-d') }}</span>
            <span class="inline"><strong>يسري هذا الترخيص لمدة:</strong> {{ $modah }} من تاريخ إصداره</span>
        </p>
        <div class="footer">
            <p><strong>مدير مكتب الرخص التجارية سبها</strong></p>
            <h3>محمد سلطان</h3>
        </div>
        <!-- إضافة رمز QR هنا -->
        <div>
            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->generate($record->licenseNumber) !!}
        </div>


    </div>
</body>
</html>
