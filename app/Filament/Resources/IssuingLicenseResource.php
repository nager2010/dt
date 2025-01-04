<?php

namespace App\Filament\Resources;

use App\Enum\LicenseType;
use Mpdf\Output\Destination;
use Illuminate\Support\Collection;
use App\Filament\Resources\IssuingLicenseResource\Pages;
use App\Models\IssuingLicense;
use App\Models\Municipality;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ViewField;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use NumberToWords\NumberToWords;
use App\Helpers\NumberHelper;



class IssuingLicenseResource extends Resource

{

    protected static ?string $model = IssuingLicense::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'اصدار الرخص';

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_issuing_licenses');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_issuing_licenses');
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()->can('edit_issuing_licenses');
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()->can('delete_issuing_licenses');
    }

    protected static ?string $recordTitleAttribute = 'projectName';

    protected static ?string $navigationGroup = 'الرخص';

    protected static ?int $navigationSort = 2;

    public static function form(Forms\Form $form): Forms\Form
    {

        return $form
            ->schema([
                Wizard::make([
                    // المرحلة الأولى: البيانات الشخصية
                    Step::make('البيانات الشخصية')
                        ->description('أدخل المعلومات الشخصية')
                        ->schema([
                            TextInput::make('fullName')
                                ->label('الاسم الرباعي')
                                ->required()
                                ->maxLength(120),
                            TextInput::make('nationalID')
                                ->label('الرقم الوطني')
                                ->required()
                                ->maxLength(20),
                            TextInput::make('passportOrID')
                                ->label('رقم جواز أو بطاقة')
                                ->maxLength(20),
                            TextInput::make('phoneNumber')
                                ->label('رقم الهاتف')
                                ->tel()
                                ->required()
                                ->maxLength(15),

                            \Filament\Forms\Components\Fieldset::make('رمز التسجيل')
                                ->schema([
                                    TextInput::make('registrationCode')
                                        ->label('رمز التسجيل') // التسمية للحقل
                                        ->numeric() // السماح فقط بالأرقام
                                        ->required() // الحقل مطلوب
                                        ->rule('regex:/^1010\d{5}$/') // يبدأ بـ 1010 ويتبعه 5 أرقام
                                        ->maxLength(9) // الحد الأقصى 9 أرقام
                                        ->validationMessages([
                                            'regex' => 'يجب أن يبدأ بـ 1010 متبوعًا بـ 5 أرقام']) // تخصيص رسالة regex
                                        ->placeholder('يجب أن يبدأ بـ 1010 متبوعًا بـ 5 أرقام'), // نص توضيحي (لاحظ الفاصلة هنا)

                                    TextInput::make('email')
                                        ->label('البريد الإلكتروني') // الحقل الجديد
                                        ->maxLength(120)
                                        ->email() // التحقق من الصلاحية كبريد إلكتروني
                                ])





                        ])->columns(2),

                    // المرحلة الثانية: بيانات الرخصة
                    Step::make('بيانات الرخصة')
                        ->description('أدخل تفاصيل الرخصة')
                        ->schema([
                            TextInput::make('projectName')
        ->label('اسم النشاط')
        ->required()
        ->validationMessages([
            'regex' => 'يجب ان لا يحتوي على حروف انجليزية او ارقام'
        ])
        ->maxLength(120)
        ->rule('regex:/^[\p{Arabic}\s]+$/u') // السماح بالأحرف العربية فقط
        ->rule(function ($get, $record) {
            return function ($attribute, $value, $fail) use ($get, $record) {
                // Convert value to UTF-8 if not already
                $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');

                $licenseTypeId = $get('license_type_id'); // الحصول على نوع الترخيص

                // إذا كان السجل موجودًا (تعديل)، استثنِ السجل الحالي من التحقق
                $exists = \App\Models\IssuingLicense::where('projectName', $value)
                    ->where('license_type_id', $licenseTypeId)
                    ->when($record, function ($query) use ($record) {
                        return $query->where('id', '!=', $record->id);
                    })
                    ->exists();

                if ($exists) {
                    $fail(mb_convert_encoding('اسم المشروع مع النوع المحدد موجود بالفعل.', 'UTF-8', 'UTF-8'));
                }
            };
        }),
                        
                        
                            TextInput::make('nearestLandmark')
                                ->label('تخصص النشاط')
                                ->maxLength(180),
                            Select::make('positionInProject')
                                ->label('صفتك في النشاط') // التسمية للحقل
                                ->options([
                                    'owner' => 'صاحب نشاط',           // خيار صاحب النشاط
                                    'general_manager' => 'مدير عام الشركة', // خيار مدير عام الشركة
                                    'chairman' => 'رئيس مجلس إدارة الشركة', // خيار رئيس مجلس الإدارة
                                ])
                                , // الحقل مطلوب

                            TextInput::make('projectAddress')
                                ->label('عنوان النشاط')
                                ->maxLength(160),

                            Select::make('municipality_id')
                                ->label('البلدية')
                                ->options(\App\Models\Municipality::pluck('name', 'id'))
                                ->searchable() // يمكن البحث في القائمة
                                ->required()
                                ->reactive() // يجعل الحقل ديناميكيًا لتحديث المحلات
                                ->afterStateUpdated(function ($state, callable $set) {
                                    // تحديث قائمة المحلات بناءً على البلدية المختارة
                                    $set('region_id', null); // إعادة ضبط اختيار المحلة
                                }),

                            // اختيار المحلة بناءً على البلدية
                            Select::make('region_id')
                                ->label('المحلة')
                                ->options(function (callable $get) {
                                    $municipalityId = $get('municipality_id');
                                    if ($municipalityId) {
                                        return \App\Models\Region::where('municipality_id', $municipalityId)->pluck('name', 'id');
                                    }
                                    return [];
                                })
                                ->searchable() // يمكن البحث في القائمة
                                ->required(),
                            Select::make('license_type_id')
                                ->label('نوع الترخيص')
                                ->options([
                                    'commercial' => 'تجاري',
                                    'industrial' => 'صناعي',
                                    'craft_service' => 'حرفي خدمي',
                                    'professional_service' => 'خدمي مهني',
                                    'general' => 'عام',
                                    'street_vendor' => 'بائع متجول',
                                    'holding_company' => 'شركة قابضة',
                                ])
                                ->required(),

                            DatePicker::make('licenseDate')
                                ->label('تاريخ اليوم') // نص التصنيف بالعربية
                                ->required() // الحقل مطلوب
                                ->default(now()->toDateString()), // تعيين التاريخ الحالي افتراض
                            TextInput::make('licenseNumber')
                                ->label('رقم الترخيص')
                                ->maxLength(15)
                                ->required()
                                ->default(fn () => Str::random(10)),
                        ])->columns(3),

                    // المرحلة الثالثة: بيانات الدفع
                    Step::make('بيانات الدفع')
                        ->description('أدخل تفاصيل الدفع')
                        ->schema([
                            TextInput::make('licenseFee')
                                ->label('رسوم الترخيص')
                                ->numeric()
                                ->required()
                                ->reactive(), // الحقل متفاعل

                            TextInput::make('licenseDuration')
                                ->label('مدة الترخيص (بالسنوات)')
                                ->numeric()
                                ->required()
                                ->minValue(1)
                                ->maxValue(10)
                                ->reactive()
                                ->afterStateUpdated(function (callable $set, $state, $get) {
                                    $licenseFee = $get('licenseFee');
                                    if ($licenseFee && $state > 0) {
                                        $set('discount', $state * $licenseFee); // حساب الخصم
                                    } else {
                                        $set('discount', 0);
                                    }

                                    // تحديث endDate في النموذج
                                    if ($state > 0) {
                                        $set('endDate', Carbon::now()->addYears($state)->toDateString());
                                    }
                                }),

                             DatePicker::make('endDate')
                              ->label('تاريخ الانتهاء')
                              ->required()
                              ->default(fn ($get) => Carbon::now()->addYears($get('licenseDuration') ?? 1)->toDateString()),
        // استخدم Carbon ��نا أيضًا
                        ])->columns(3),




                    // المرحلة الرابعة: بيانات أخرى
                    Step::make('بيانات أخرى')
                        ->description('معلومات إضافية')
                        ->schema([
                            TextInput::make('chamberOfCommerceNumber')
                                ->label('رقم السجل التجاري')
                                ->maxLength(20),
                            TextInput::make('taxNumber')
                                ->label('الرقم الضريبي')
                                ->maxLength(20),
                            TextInput::make('economicNumber')
                                ->label('رقم الايصال المالي')
                                ->maxLength(20),
                        ])->columns(3),
                ]),
            ])->columns(1);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('fullName')->label('الاسم الرباعي')->searchable()->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('nationalID')->label('الرقم الوطني')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('passportOrID')->label('رقم جواز أو بطاقة')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phoneNumber')->label('رقم الهاتف')->searchable()->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('projectName')->label('اسم المشروع')->searchable()->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('positionInProject')->label('صفتك في المشروع')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('projectAddress')->label('عنوان المشروع')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('municipality.name')
                    ->label('البلدية')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('region.name')
                    ->label('المحلة')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('nearestLandmark')->label('تخصص النشاط')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('license_type_id')
                    ->label('نوع الترخيص')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'commercial' => 'تجاري',
                            'industrial' => 'صناعي',
                            'craft_service' => 'حرفي خدمي',
                            'professional_service' => 'خدمي مهني',
                            'general' => 'عام',
                            'street_vendor' => 'بائع متجول',
                            'holding_company' => 'شركة قابضة',
                            default => 'غير محدد', // حالة افتراضية إذا كانت القيمة غير معروفة
                        };
                    }),

                TextColumn::make('licenseDate')->label('تاريخ اليوم')->date()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('endDate')->label('تاريخ انتهاء الصلاحية')->date()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('licenseNumber')->label('رقم الترخيص')->searchable()->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('licenseDuration')->label('مدة الترخيص')->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('modah')
                    ->label('المدة بالحروف')->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->getStateUsing(fn ($record) => NumberHelper::yearsToWords($record->licenseDuration)),
                TextColumn::make('licenseFee')->label('رسوم الترخيص')->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('discount')
                    ->label('الإجمالي')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ejmalybelhroof')
                    ->label('الإجمالي بالحروف')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->getStateUsing(function ($record) {
                        return isset($record->discount) && $record->discount > 0
                            ? \App\Helpers\NumberHelper::amountToWords($record->discount)
                            : 'لا توجد بيانات';
                    }),

                TextColumn::make('remainingDays')
                    ->label('الأيام المتبقية')
                    ->formatStateUsing(function ($record) {
                        if (!$record->endDate) {
                            return 'غير محدد'; // إذا لم يتم تحديد تاريخ انتهاء
                        }

                        $remainingDays = \Carbon\Carbon::parse($record->endDate)->diffInDays(now(), false);

                        if ($remainingDays < 0) {
                            // إذا كانت الأيام سالبة، الرخصة منتهية
                            return "المتبقي " . abs($remainingDays) . " يوم";
                        } elseif ($remainingDays === 0) {
                            // إذا كانت الصلاحية تنتهي اليوم
                            return "تنتهي اليوم";
                        } else {
                            // إذا كانت الأيام موجبة، الرخصة صالحة
                            return "منتهية منذ {$remainingDays} يوم";
                        }
                    })

        ->sortable(),




        TextColumn::make('chamberOfCommerceNumber')->label('رقم الايصال المالي')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('taxNumber')->label('الرقم الضريبي')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('economicNumber')->label('رقم السجل التجاري')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label('تاريخ الإنشاء')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label('تاريخ التحديث')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                ViewAction::make()
                    ->label('عرض للطباعة') // تسمية الإجراء
                    ->modalHeading('تفاصيل السجل') // عنوان النافذة
                    ->form([
                        Forms\Components\Grid::make(3) // تقسيم الحقول إلى 3 أعمدة
                        ->schema([

                            Select::make('license_type_id')
                                ->label('نوع الترخيص')
                                ->disabled()
                                ->columnSpan(1),
                            TextInput::make('licenseNumber')
                                ->label('رقم الترخيص')
                                ->disabled()
                                ->columnSpan(1),
                            DatePicker::make('licenseDate')
                                 ->label('تاريخ الترخيص')
                                ->disabled()
                                 ->columnSpan(1),
                            TextInput::make('fullName')
                                ->prefix(' بعد الاطلاع على الطلب المقدم من السيد / ة :')
                                ->label('الاسم الرباعي')
                                ->columnSpan(3)
                                ->disabled(),
                            TextInput::make('passportOrID')
                                ->label('رقم الجواز أو البطاقة')
                                ->columnSpan(1)
                                ->disabled(),
                            TextInput::make('positionInProject')
                                ->label('صفتك في المشروع')
                                ->prefix('بصفته :')
                                ->columnSpan(2)
                                ->disabled(),
                            Fieldset::make('يرخص')
                                ->schema([
                                    TextInput::make('fullName')
                                        ->prefix('يرخص للسيد / ة :')
                                        ->label('الاسم الرباعي')
                                        ->columnSpan(2)
                                        ->disabled(),
                                    TextInput::make('taxNumber')
                                        ->label('رقم الضريبي')
                                        ->columnSpan(1)
                                        ->disabled(),
                                    TextInput::make('chamberOfCommerceNumber')
                                        ->label('رقم الايصال المالي')
                                        ->columnSpan(1)
                                        ->disabled(),
                                ])->columnSpan(3),
                            Select::make('region_id')
                                ->label('المحلة')
                                ->options(function (callable $get) {
                                    $municipalityId = $get('municipality_id');
                                    return $municipalityId ? \App\Models\Region::where('municipality_id', $municipalityId)->pluck('name', 'id') : [];
                                })
                                ->disabled(),
                            TextInput::make('projectName')
                                ->label('اسم التجاري')
                                ->disabled(),
                            // TextInput::make('remainingDays')
                            //     ->label('الأيام المتبقية')
                            //     ->disabled(),
                            TextInput::make('discount')
                                ->label('الإجمالي')
                                ->disabled()
                                ->columnSpan(1), // يأخذ عمودًا واحدًا

                            TextInput::make('ejmalybelhroof')
                                ->label('الإجمالي بالحروف')
                                ->disabled()
                                ->default(fn ($record) => $record->discount ? \App\Helpers\NumberHelper::amountToWords($record->discount) : 'لا توجد بيانات')
                                ->columnSpan(2),

                            TextInput::make('modah')
                                ->label('المدة بالحروف')
                                ->disabled()
                                ->default(fn ($record) => $record->licenseDuration ? \App\Helpers\NumberHelper::yearsToWords($record->licenseDuration) : 'لا توجد بيانات'),

                            Fieldset::make('QR كود')
                                ->schema([
                                    ViewField::make('qrcode')
                                        ->label('QR Code')
                                        ->view('components.qrcode')
                                        ->statePath('licenseNumber') // ربط QR Code بالحقل "licenseNumber"
                                        ->columnSpan(1), // عرض QR Code يأخذ عمودًا واحدًا من 3
                                ])->columnSpan(3),

                        ]),
                    ])
                    ->mutateRecordDataUsing(function (array $data): array {
                        // تعديل البيانات قبل العرض
                        $data['remainingDays'] = $data['endDate']
                            ? \Carbon\Carbon::parse($data['endDate'])->diffInDays(now(), false) . ' يوم'
                            : 'غير محدد';

                        return $data;
                 }),

        //add printing
                Action::make('print')
    ->label('طباعة')
    ->icon('heroicon-o-printer')
    ->action(function ($record) {
        return \App\Filament\Resources\IssuingLicenseResource::printRecord($record);
    })
    ->color('primary'),
           ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('print')
                    ->label('طباعة التقرير')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->action(function (Collection $licenses) {
                        return static::printFilteredRecords($licenses);
                    })
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public function licenseType()
    {
        return $this->belongsTo(\App\Models\LicenseType::class, 'license_type_id');
    }


    public static function getWidgets(): array
    {
        return []; // مصفوفة فارغة لإخفاء الودجات
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIssuingLicenses::route('/'),
            'create' => Pages\CreateIssuingLicense::route('/create'),
//            'edit' => Pages\EditIssuingLicense::route('/{record}/edit'),
        ];
    }


    public static function printRecord($record)
    {
        // إعداد مسار الخطوط
        $fontDirs = (new \Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'];
        $fontDirs[] = resource_path('fonts');

        // إعداد بيانات الخطوط
        $fontData = (new \Mpdf\Config\FontVariables())->getDefaults()['fontdata'];
        $fontData['amiri'] = [
            'R' => 'Amiri-Regular.ttf',
            'B' => 'Amiri-Bold.ttf',
        ];

        // إنشاء كائن Mpdf
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8', 
            'format' => 'A4', 
            'default_font' => 'amiri',
            'tempDir' => storage_path('temp'),
            'fontDir' => array_merge($fontDirs, [
                resource_path('fonts'),
            ]),
            'fontdata' => $fontData,
            'default_font_size' => 12,
        ]);

        $mpdf->SetDirectionality('rtl');
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        // حساب "المدة بالحروف"
        $modah = isset($record->licenseDuration)
            ? \App\Helpers\NumberHelper::amountToWords($record->licenseDuration)
            : '';

        // حساب "الإجمالي بالحروف"
        $ejmalybelhroof = isset($record->totalAmount)
            ? \App\Helpers\NumberHelper::amountToWords($record->totalAmount)
            : '';

        // تحميل قالب البليد مع البيانات
        $html = view('reports.single_license', [
            'record' => $record,
            'modah'  => $modah,
            'ejmalybelhroof' => $ejmalybelhroof,
        ])->render();

        // Ensure proper UTF-8 encoding
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        
        // كتابة التقرير إلى ملف PDF
        $mpdf->WriteHTML($html);

        // عرض ملف PDF مباشرة
        return response($mpdf->Output('license-' . $record->id . '.pdf', 'S'), 200)
            ->header('Content-Type', 'application/pdf; charset=utf-8');
    }


    public static function printFilteredRecords($records)
    {
        // Create PDF instance with basic UTF-8 configuration
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'default_font' => 'dejavusans',
            'tempDir' => storage_path('temp')
        ]);

        $mpdf->SetDirectionality('rtl');
        
        // Convert the view to UTF-8 encoded HTML
        $html = view('pdf.licenses-report', [
            'licenses' => $records,
            'filters' => request()->query(),
        ])->render();

        // Ensure UTF-8 encoding
        $html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
        
        // Write content
        $mpdf->WriteHTML($html);

        // Output as string with UTF-8 headers
        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename=licenses-report.pdf');
    }


    protected static function numberToWords($number, $locale = 'ar'): string
    {
        $formatter = new \NumberFormatter($locale, \NumberFormatter::SPELLOUT);
        return $formatter->format($number);
    }


}
