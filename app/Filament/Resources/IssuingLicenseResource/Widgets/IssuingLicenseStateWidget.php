<?php

namespace App\Filament\Resources\IssuingLicenseResource\Widgets;

use App\Models\IssuingLicense;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class IssuingLicenseStateWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // حساب عدد الشركات
        // حساب عدد السجلات التي تحتوي على "شركات" في حقل "type"
        $companyCount = IssuingLicense::where('license_type_id','company')->count();
        // حساب عدد الأنشطة الفردية
        $individualActivitiesCount = IssuingLicense::where('license_type_id','individual')->count();

        // حساب إجمالي الرخص
        $totalLicensesCount = IssuingLicense::count();

        return [
            Stat::make('الشركات', $companyCount)
                ->description('عدد الشركات')
               ->descriptionIcon('heroicon-o-briefcase')
               ->color('gray')
                ->chart([$companyCount, $totalLicensesCount]),


            Stat::make('الأنشطة الفردية', $individualActivitiesCount)
                ->description('عدد الأنشطة الفردية')
                ->descriptionIcon('heroicon-o-user')
                ->color('warning')
                ->chart([$individualActivitiesCount, $totalLicensesCount]),

            Stat::make('إجمالي الرخص', $totalLicensesCount)
                ->description('مجموع الرخص بالكامل')
               ->descriptionIcon('heroicon-o-user-group')

                ->chart([$individualActivitiesCount, $totalLicensesCount, $companyCount]),
        ];
    }
}
