<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LicenseRequestResource\Pages;
use App\Models\LicenseRequest;
use App\Models\IssuingLicense;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Filament\Forms\Components\Html;


class LicenseRequestResource extends Resource
{
    protected static ?string $model = LicenseRequest::class;

    public static function canViewAny(): bool
    {
        return true; // السماح لجميع المستخدمين بعرض المورد
    }

    public static function canCreate(): bool
    {
        return true; // السماح لجميع المستخدمين بإنشاء السجلات
    }


    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false; // منع التعديل
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false; // منع الحذف
    }



    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'طلبات التراخيص';

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['licenseFee'] = 500; // تعيين قيمة ثابتة
        $data['licenseDuration'] = 1;
        $data['endDate'] = now()->addYears(1)->toDateString();

        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['licenseDuration'])) {
            $data['endDate'] = now()->addYears($data['licenseDuration'])->toDateString();
        }

        return $data;
    }




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
                                ->placeholder(' الشارع - رقم المبنى'),
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
                                ->default(500), // القيم الافتراضية
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
            ])->columns(1);
    }


        public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fullName')->label('الاسم الرباعي'),
                Tables\Columns\TextColumn::make('projectName')->label('اسم النشاط'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'warning' => 'Pending',
                        'success' => 'Approved',
                        'danger' => 'Rejected',
                    ])
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'Pending' => 'قيد المراجعة',
                            'Approved' => 'موافقة',
                            'Rejected' => 'مرفوضة',
                            default => 'غير معروف',
                        };
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'Pending' => 'قيد المراجعة',
                        'Approved' => 'موافقة',
                        'Rejected' => 'مرفوضة',
                    ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->actions([
                Action::make('approve')
    ->label('موافقة')
    ->color('success')
    ->form([
        TextInput::make('fullName')
            ->label('الاسم الرباعي')
            ->default(fn ($record) => $record->fullName)
            ->rule('regex:/^[\p{Arabic}\s]+$/u') // التحقق من الأحرف العربية فقط
            ->validationMessages([
                'regex' => 'يجب أن يحتوي الاسم على أحرف عربية فقط.',
            ]),

            TextInput::make('nationalID')
            ->label('الرقم الوطني')
            ->default(fn ($record) => $record->nationalID)
            ->rule('regex:/^(119|120|219|220)\d{8}$/') // يجب أن يبدأ بـ 119 أو 120 أو 219 أو 220 ويليه 8 أرقام
            ->validationMessages([
                'regex' => 'الرقم الوطني يجب أن يبدأ بـ 1 أو 2 ويتكون من 12 رقمًا.',
            ]),
        

            TextInput::make('passportOrID')
            ->label('رقم جواز أو بطاقة')
            ->default(fn ($record) => $record->passportOrID)
            ->rule('regex:/^[A-Za-z0-9]{5,10}$/') // التحقق من أن الحروف والأرقام إنجليزية فقط ويتراوح الطول بين 5 إلى 10
            ->validationMessages([
                'regex' => 'رقم جواز السفر أو البطاقة يجب أن يتكون من 5 إلى 10 حروف أو أرقام إنجليزية فقط.',
            ]),
        

        TextInput::make('phoneNumber')
            ->label('رقم الهاتف')
            ->default(fn ($record) => $record->phoneNumber)
            ->rule('regex:/^(\+218|0)\d{9}$/') // التحقق من رقم الهاتف الليبي
            ->validationMessages([
                'regex' => 'رقم الهاتف يجب أن يكون بصيغة ليبية (+218 أو 0 تليها 9 أرقام).',
            ]),

        TextInput::make('registrationCode')
            ->label('رمز التسجيل')
            ->default(fn ($record) => $record->registrationCode)
            ->rule('regex:/^1010\d{5}$/') // التحقق من أن الرمز يبدأ بـ 1010 ويتبعها 5 أرقام
            ->validationMessages([
                'regex' => 'رمز التسجيل يجب أن يبدأ بـ 1010 متبوعًا بـ 5 أرقام.',
            ]),

        TextInput::make('email')
            ->label('البريد الإلكتروني')
            ->default(fn ($record) => $record->email)
            ->email() // التحقق من صيغة البريد الإلكتروني
            ->validationMessages([
                'email' => 'يجب إدخال بريد إلكتروني صالح.',
            ]),

        TextInput::make('projectName')
            ->label('اسم النشاط')
            ->default(fn ($record) => $record->projectName)
            ->rule('regex:/^[\p{Arabic}\s]+$/u') // التحقق من الأحرف العربية فقط
            ->validationMessages([
                'regex' => 'اسم النشاط يجب أن يحتوي على أحرف عربية فقط.',
            ]),

        TextInput::make('licenseFee')
            ->label('رسوم الترخيص')
            ->numeric()
            ->required()
            ->placeholder('أدخل رسوم الترخيص (في حالة عدم وجوده)'),

            TextInput::make('licenseDuration')
            ->label('مدة الترخيص (بالسنوات)')
            ->numeric()
            ->required()
            ->minValue(1)
            ->maxValue(10)
            ->placeholder('أدخل مدة الترخيص (في حالة عدم وجودها)')
            ->reactive() // يجعل الحقل ديناميكيًا
            ->afterStateUpdated(function (callable $set, $state) {
                if ($state) {
                    // تحديث تاريخ الانتهاء بناءً على مدة الترخيص
                    $set('endDate', now()->addYears($state)->toDateString());
                }
            }),
        
        DatePicker::make('licenseDate')
            ->label('تاريخ الإصدار')
            ->default(now()->toDateString())
            ->required(),
        
        DatePicker::make('endDate')
            ->label('تاريخ الانتهاء')
            ->required()
            ->default(fn ($get) => now()->addYears($get('licenseDuration') ?? 1)->toDateString()),
        
    ])
    ->action(function ($record, $data) {
        // إنشاء رقم ترخيص إذا لم يكن موجودًا
        $licenseNumber = $record->licenseNumber ?? 'LN-' . strtoupper(Str::random(8));

        // نقل البيانات إلى جدول التراخيص
        \App\Models\IssuingLicense::create([
            'licenseNumber' => $licenseNumber,
            'licenseFee' => $data['licenseFee'],
            'licenseDuration' => $data['licenseDuration'],
            'licenseDate' => $data['licenseDate'],
            'endDate' => $data['endDate'],
            'fullName' => $record->fullName,
            'nationalID' => $record->nationalID,
            'passportOrID' => $record->passportOrID,
            'phoneNumber' => $record->phoneNumber,
            'registrationCode' => $record->registrationCode,
            'email' => $record->email,
            'projectName' => $record->projectName,
            'nearestLandmark' => $record->nearestLandmark,
            'positionInProject' => $record->positionInProject,
            'projectAddress' => $record->projectAddress,
            'municipality_id' => $record->municipality_id,
            'region_id' => $record->region_id,
            'license_type_id' => $record->license_type_id,
            'chamberOfCommerceNumber' => $record->chamberOfCommerceNumber,
            'taxNumber' => $record->taxNumber,
            'economicNumber' => $record->economicNumber,
        ]);

        // تحديث حالة الطلب
        $record->update(['status' => 'Approved']);

        Notification::make()
            ->title('تمت الموافقة')
            ->body('تمت الموافقة على الطلب وإضافة البيانات المطلوبة.')
            ->success()
            ->send();
    })
    ->requiresConfirmation()
    ->modalHeading('موافقة الطلب')
    ->visible(fn ($record) => $record->status === 'Pending'),
               

                Action::make('reject')
                    ->label('رفض')
                    ->color('danger')
                    ->form([
                        Textarea::make('admin_note')
                            ->label('سبب الرفض'),
                    ])
                    ->action(function ($record, $data) {
                        $record->update([
                            'status' => 'Rejected',
                            'admin_note' => $data['admin_note'],
                        ]);

                        Notification::make()
                            ->title('تم الرفض')
                            ->body('تم رفض الطلب بنجاح.')
                            ->danger()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'Pending'),


                Tables\Actions\EditAction::make(),
                
                
            ]);
            

    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLicenseRequests::route('/'),
            'create' => Pages\CreateLicenseRequest::route('/create'),
            'edit' => Pages\EditLicenseRequest::route('/{record}/edit'),
        ];
    }


}
