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
            <span class="inline"><strong>التاريخ:</strong> {{ \Carbon\Carbon::parse($record->licenseDate)->format('y-m-d') }}</span>
        </p>
        <p>
            <span class="inline"><strong>بعد الاطلاع على الطلب المقدم من السيد / ة:</strong> {{ $record->fullName }}</span>
        </p>
        <p>
            <span class="inline"><strong>رقم جواز سفر أو بطاقة شخصية:</strong> {{ $record->passportOrID }}</span>
            <span class="inline"><strong>بصفته:</strong>
                {{
           match($record->positionInProject) {
                'owner' => 'صاحب نشاط',           // خيار صاحب النشاط
                'general_manager' => 'مدير عام الشركة', // خيار مدير عام الشركة
                'chairman' => 'رئيس مجلس إدارة الشركة', // خيار رئيس مجلس الإدارة
                 default => 'غير محدد',
           }
       }}</span>
        </p>
        <p class="center-title">يرخص</p>

        <p>
            @if($record->positionInProject === 'owner')
                <span class="inline"><strong>للسيد/ة (الاسم الرباعي):</strong> {{ $record->fullName }}</span>
            @elseif($record->positionInProject === 'general_manager' || $record->positionInProject === 'chairman')
                <span class="inline"><strong>لشركة:</strong> {{ $record->projectName }}</span>
            @else
                <span class="inline"><strong>الجهة:</strong> غير محددة</span>
            @endif
        </p>

        <p>
            <span class="inline"><strong>الرقم الضريبي:</strong> {{ $record->taxNumber ?? '.........' }}</span>
            <span class="inline"><strong>سجل تجاري رقم:</strong> {{ $record->economicNumber ?? '.........' }}</span>
        </p>
        <p>
            <span class="inline"><strong>بمزاولة نشاط:</strong> {{ $record->nearestLandmark }}</span>
            <span class="inline">
    <strong>بمقرها الكائن:</strong>
    {{ $record->municipality->name ?? 'سبها' }} - {{ $record->region->name ?? '........' }}
</span>

        </p>
        <p>
            <span class="inline"><strong>الاسم التجاري:</strong> {{ $record->projectName }}</span>
            <span class="inline"><strong>البريد الإلكتروني:</strong> {{ $record->email ?? '................'  }}</span>
        </p>
        <table class="table" dir="ltr"> <!-- تحديد الاتجاه من اليسار لليمين -->
            <tr>
                @foreach(str_split($record->registrationCode) as $digit)
                    <td>{{ $digit }}</td>
                @endforeach
                <td><strong>سجلت تحت رمز</strong></td> <!-- إضافة الخلية في نهاية الصف -->
            </tr>
        </table>

        <p>
            <span class="inline"><strong>هذه الرخصة صالحة إلى:</strong> {{ \Carbon\Carbon::parse($record->endDate)->format('y-m-d') }}</span>
            <span class="inline"><strong>المدة بالحروف:</strong> {{ $modah }} من تاريخ الإصدار</span>
        </p>
        <p>
            <span class="inline"><strong>سددت الرسوم بواقع:</strong> {{ $record->discount }} د.ل</span>
            <span class="inline"><strong>بالحروف:</strong> {{ $ejmalybelhroof }}</span>
        </p>
        <p>
            <span class="inline"><strong>بموجب إيصال مالي:</strong> {{ $record->chamberOfCommerceNumber }}</span>
            <span class="inline"><strong>بتاريخ:</strong> {{ \Carbon\Carbon::parse($record->licenseDate)->format('y-m-d') }}</span>
        </p>
        <p>
            <span class="inline"><strong>صدرت بتاريخ:</strong> {{ \Carbon\Carbon::parse($record->licenseDate)->format('y-m-d') }}</span>
            <span class="inline"><strong>يسري هذا الترخيص لمدة:</strong> {{ $modah }} من تاريخ إصداره</span>
        </p>
        <div class="footer" style="display: flex; align-items: center; justify-content: space-between; font-size: 18px; padding: 10px;">
            <!-- رمز QR على اليمين -->
            <div style="text-align: right; color: white;">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(50)->generate($record->licenseNumber) !!}
            </div>
            <div style="text-align: left;">

                <p style="font-size: 18px; font-weight: bold; margin-top: 5px;">مدير مكتب التراخيص {{ $record->municipality->name ?? 'بالبلدية' }} </p>
            </div>

            <!-- النصوص على اليسار -->
            <div style="text-align: left;">
                <p style="font-size: 24px; font-weight: bold; margin-left: 30PX;">محمد سلطان</p>
            </div>
        </div>



    </div>
</body>
</html>
