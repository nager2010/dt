<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Pages\Page;
use Filament\Forms;
use App\Models\LicenseRequest;

class PublicLicenseForm extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-pencil';
    protected static string $view = 'filament.pages.public-license-form';

    public $formState = []; // لحفظ البيانات قبل الإرسال

    public function submit()
    {
        // التحقق من البيانات وحفظها
        $validatedData = $this->form->getState();

        LicenseRequest::create($validatedData);

        session()->flash('success', 'تم تسجيل الرخصة بنجاح.');

        return redirect()->to('/thank-you');
    }

    protected function getFormSchema(): array
    {
        return [
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
                            ->label('رقم جواز السفر أو البطاقة')
                            ->maxLength(20),
                        TextInput::make('phoneNumber')
                            ->label('رقم الهاتف')
                            ->required()
                            ->tel()
                            ->maxLength(15),
                        TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->maxLength(120),
                        TextInput::make('registrationCode')
                            ->label('رمز التسجيل')
                            ->required()
                            ->numeric()
                            ->rule('regex:/^1010\d{5}$/')
                            ->maxLength(9)
                            ->default(fn () => '1010' . str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT)) // تعبئة تلقائية
                            ->validationMessages([
                                'regex' => 'يجب أن يبدأ بـ 1010 متبوعًا بـ 5 أرقام.',
                            ])
                            ->placeholder('سيتم تعبئته تلقائيًا'),
                    ])->columns(2),

                // المرحلة الثانية: بيانات الرخصة
                Step::make('بيانات الرخصة')
                    ->description('أدخل تفاصيل الرخصة')
                    ->schema([
                        TextInput::make('projectName')
                            ->label('اسم النشاط')
                            ->required()
                            ->rule('regex:/^[\p{Arabic}\s]+$/u')
                            ->validationMessages([
                                'regex' => 'يجب أن يحتوي فقط على أحرف عربية ومسافات.',
                            ])
                            ->maxLength(120)
                            ->placeholder('مثل: مطعم الأمير او شركة العلياء المحدودة'),
                        Select::make('positionInProject')
                            ->label('الصفة في النشاط')
                            ->options([
                                'owner' => 'صاحب النشاط',
                                'general_manager' => 'مدير عام',
                                'chairman' => 'رئيس مجلس الإدارة',
                            ])
                            ->required(),
                        TextInput::make('projectAddress')
                            ->label('عنوان النشاط')
                            ->maxLength(160)
                            ->placeholder('الشارع - رقم المبنى'),
                        Select::make('municipality_id')
                            ->label('البلدية')
                            ->options(\App\Models\Municipality::pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('region_id', null);
                            }),
                        Select::make('region_id')
                            ->label('المحلة')
                            ->options(function (callable $get) {
                                $municipalityId = $get('municipality_id');
                                return $municipalityId
                                    ? \App\Models\Region::where('municipality_id', $municipalityId)->pluck('name', 'id')
                                    : [];
                            })
                            ->searchable()
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
                        TextInput::make('nearestLandmark')
                            ->label('تخصص النشاط')
                            ->maxLength(180)
                            ->placeholder('مثل:بيع المواد الغذائية'),
                        DatePicker::make('licenseDate')
                            ->label('تاريخ اليوم')
                            ->required()
                            ->default(now()->toDateString()),
                        TextInput::make('licenseFee')
                            ->label('رسوم الترخيص')
                            ->numeric()
                            ->required()
                            ->default(500),
                        TextInput::make('licenseDuration')
                            ->label('مدة الترخيص')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->reactive()
                            ->afterStateUpdated(function ($set, $state) {
                                $set('endDate', now()->addYears($state)->toDateString());
                            }),
                        DatePicker::make('endDate')
                            ->label('تاريخ الانتهاء')
                            ->required(),
                    ])->columns(3),

                // المرحلة الثالثة: ملاحظات المسؤول
                Step::make('ملاحظات المسؤول')
                    ->description('إضافة ملاحظات المسؤول حول الطلب')
                    ->schema([
                        Textarea::make('admin_note')
                            ->label('ملاحظات المدير')
                            ->columnSpanFull()
                            ->nullable(),
                        TextInput::make('status')
                            ->label('الحالة')
                            ->default('Pending')
                            ->disabled(),
                    ])->columns(1),
            ]),
        ];
    }
}
