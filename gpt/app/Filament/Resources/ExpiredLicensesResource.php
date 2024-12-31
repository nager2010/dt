<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpiredLicensesResource\Pages;
use App\Models\IssuingLicense;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Carbon\Carbon;


class ExpiredLicensesResource extends Resource
{
    protected static ?string $model = IssuingLicense::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'الرخص المنتهية';
    protected static ?string $slug = 'expired-licenses';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([]);
    }

    public static function getHeaderActions(): array
    {
        return [
            Action::make('إجمالي الفاقد')
                ->label('إجمالي الفاقد')
                ->icon('heroicon-o-calculator')
                ->action(function () {
                    $totalLoss = IssuingLicense::where('endDate', '<', now())->sum('licenseFee');
                    Notification::make()
                        ->title('إجمالي الفاقد')
                        ->body("إجمالي رسوم الرخص المنتهية: {$totalLoss} دينار")
                        ->send();
                })
                ->color('danger'),
        ];
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(
                IssuingLicense::query()
            )
            ->columns([
                TextColumn::make('fullName')
                    ->label('الاسم الرباعي')
                    ->searchable()
                    ->sortable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('projectName')
                    ->label('اسم المشروع')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('licenseDate')
                    ->label('تاريخ الإصدار')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable()
                    ->date(),

                TextColumn::make('endDate')
                    ->label('تاريخ الانتهاء')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),

                TextColumn::make('licenseFee')
                    ->label('رسوم الترخيص')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('الحالة')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->colors([
                        'danger' => fn ($record) => $record->endDate && $record->endDate < now(),
                        'success' => fn ($record) => $record->endDate && $record->endDate >= now(),
                        'secondary' => fn ($record) => !$record->endDate,
                    ])
                    ->formatStateUsing(function ($record) {
                        if (!$record->endDate) {
                            return 'قيد الإجراء';
                        }
                        return $record->endDate > now() ? 'سارية' : 'تحتاج تجديد';
                    }),
            ])
            ->filters([
                Filter::make('منتهية')
                    ->query(fn($query) => $query->where('endDate', '<', now()))
                    ->label('الرخص المنتهية'),
            ])
            ->defaultSort('endDate', 'desc')
            ->actions([
                Action::make('تجديد')
                    ->label('تجديد')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        TextInput::make('licenseFee')
                            ->label('رسوم الترخيص')
                            ->numeric()
                            ->required(),

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

                                // تحديث endDate بناءً على المدة الجديدة
                                if ($state > 0) {
                                    $set('endDate', Carbon::now()->addYears($state)->toDateString());
                                }
                            }),

                        DatePicker::make('licenseDate')
                            ->label('تاريخ الإصدار')
                            ->required()
                            ->default(Carbon::now()->toDateString()),

                        DatePicker::make('endDate')
                            ->label('تاريخ الانتهاء')
                            ->required()
                            ->default(fn ($get) => Carbon::now()->addYears($get('licenseDuration') ?? 1)->toDateString()),
                    ])
                    ->action(function ($record, $data) {
                        // تحديث البيانات في قاعدة البيانات
                        $record->update([
                            'licenseFee' => $data['licenseFee'],
                            'licenseDuration' => $data['licenseDuration'],
                            'licenseDate' => $data['licenseDate'], // تحديث تاريخ الإصدار
                            'endDate' => $data['endDate'],         // تحديث تاريخ الانتهاء
                        ]);

                        // إرسال إشعار بعد التحديث
                        Notification::make()
                            ->title('تم التجديد بنجاح')
                            ->body("تم تجديد الترخيص الخاص بـ {$record->fullName}.")
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation() // طلب تأكيد قبل الإجراء
                    ->modalHeading('تجديد الترخيص') // عنوان المودال
                    ->color('success') // تخصيص اللون
                    ->visible(fn ($record) => $record->endDate && $record->endDate < now()), // إخفاء الزر إذا كانت الحالة "سارية"
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);


    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpiredLicenses::route('/'),
        ];
    }
}
