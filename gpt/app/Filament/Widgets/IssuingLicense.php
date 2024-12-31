<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\IssuingLicense as IssuingLicenseModel;
use Carbon\Carbon;

class IssuingLicense extends BaseWidget
{
    protected function getStats(): array
    {
        // إجمالي الرسوم المدفوعة
        $totalDiscount = IssuingLicenseModel::sum('discount');

        // عدد الرخص المنتهية
        $expiredLicenses = IssuingLicenseModel::where('endDate', '<', now())->count();

        // حساب الفاقد من الرسوم بناءً على الرخص المنتهية
        $totalLost = IssuingLicenseModel::where('endDate', '<', now())->sum('licenseFee');

        // عدد الأيام المتبقية للرخص السارية
        $remainingLicenses = IssuingLicenseModel::where('endDate', '>=', now())->get()
            ->map(function ($license) {
                $remainingDays = Carbon::parse($license->endDate)->diffInDays(now(), false);
                return $remainingDays > 0 ? $remainingDays : 0;
            })->sum();

        return [
            Stat::make('الرخص المنتهية الصلاحية', $expiredLicenses . ' رخصة')
                ->description('عدد الرخص التي انتهت صلاحيتها')
                ->descriptionIcon('heroicon-o-exclamation-circle')
                ->color('danger')
                ->chart([$expiredLicenses, $remainingLicenses]),

            Stat::make('اجمالي الخزينة', number_format($totalDiscount, 2) . ' د.ل')
                ->description('إجمالي الرسوم المالية المدفوعة')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success')
                ->chart([$totalDiscount, $totalLost]),

            Stat::make('الفاقد من الرخص', number_format($totalLost, 2) . ' د.ل')
                ->description('إجمالي الرسوم المالية المفقودة')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('danger')
                ->chart([$totalLost, $totalDiscount]),
        ];
    }
}
