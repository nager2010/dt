<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpiredLicensesResource\Pages;
use App\Models\IssuingLicense;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;

class ExpiredLicensesResource extends Resource
{
    protected static ?string $model = IssuingLicense::class; // الربط مع الموديل
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'الرخص المنتهية';
    protected static ?string $slug = 'expired-licenses';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([]); // لا حاجة إلى نموذج
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
                IssuingLicense::query() // عرض جميع البيانات
            )
            ->columns([
                TextColumn::make('fullName')
                    ->label('الاسم الرباعي')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('projectName')
                    ->label('اسم المشروع')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('licenseDate')
                    ->label('تاريخ الإصدار')
                    ->date(),

                TextColumn::make('endDate')
                    ->label('تاريخ الانتهاء')
                    ->date()
                    ->sortable(),

                TextColumn::make('licenseFee')
                    ->label('رسوم الترخيص')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('الحالة')
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
            ->defaultSort('endDate', 'desc');
    }



    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpiredLicenses::route('/'),
        ];
    }
}
