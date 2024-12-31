<?php

namespace App\Filament\Resources\IssuingLicenseResource\Widgets;

use App\Models\IssuingLicense;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class IssuingLicenseStateWidget extends BaseWidget
{
    // لا حاجة لتغيير $columnSpan على مستوى الكلاس

    protected function getStats(): array
    {
        // حساب الإحصائيات
        $companyCount = IssuingLicense::where('license_type_id', 'company')->count();
        $individualActivitiesCount = IssuingLicense::where('license_type_id', 'individual')->count();
        $totalLicensesCount = IssuingLicense::count();
        $commercialCount = IssuingLicense::where('license_type_id', 'commercial')->count();
        $industrialCount = IssuingLicense::where('license_type_id', 'industrial')->count();
        $craftServiceCount = IssuingLicense::where('license_type_id', 'craft_service')->count();
        $professionalServiceCount = IssuingLicense::where('license_type_id', 'professional_service')->count();
        $generalCount = IssuingLicense::where('license_type_id', 'general')->count();
        $streetVendorCount = IssuingLicense::where('license_type_id', 'street_vendor')->count();
        $holdingCompanyCount = IssuingLicense::where('license_type_id', 'holding_company')->count();

        return [
            // إجمالي الرخص يشغل عرض الشبكة بالكامل
            Stat::make('إجمالي الرخص', $totalLicensesCount)
                ->description('مجموع الرخص بالكامل')
                ->descriptionIcon('heroicon-o-user-group')
                ->chart([$individualActivitiesCount, $totalLicensesCount, $companyCount])
                ->color('primary')
                ->extraAttributes(['class' => 'col-span-4']), // امتداد كامل للشبكة

            // الإحصائيات الأخرى بحجم قياسي
            Stat::make('تجاري', $commercialCount)
                ->description('عدد الرخص التجارية')
                ->descriptionIcon('heroicon-o-building-office')
                ->color('info')
                ->chart([$commercialCount, $totalLicensesCount])
                ->extraAttributes(['class' => 'col-span-1']),

            Stat::make('صناعي', $industrialCount)
                ->description('عدد الرخص الصناعية')
                ->descriptionIcon('heroicon-o-cog')
                ->color('info')
                ->chart([$industrialCount, $totalLicensesCount])
                ->extraAttributes(['class' => 'col-span-1']),

            Stat::make('حرفي خدمي', $craftServiceCount)
                ->description('عدد الرخص الحرفية الخدمية')
                ->descriptionIcon('heroicon-o-adjustments-horizontal')
                ->color('info')
                ->chart([$craftServiceCount, $totalLicensesCount])
                ->extraAttributes(['class' => 'col-span-1']),

            Stat::make('خدمي مهني', $professionalServiceCount)
                ->description('عدد الرخص المهنية الخدمية')
                ->descriptionIcon('heroicon-o-briefcase')
                ->color('info')
                ->chart([$professionalServiceCount, $totalLicensesCount])
                ->extraAttributes(['class' => 'col-span-1']),

            Stat::make('عام', $generalCount)
                ->description('عدد الرخص العامة')
                ->descriptionIcon('heroicon-o-globe-alt')
                ->color('info')
                ->chart([$generalCount, $totalLicensesCount])
                ->extraAttributes(['class' => 'col-span-1']),

            Stat::make('بائع متجول', $streetVendorCount)
                ->description('عدد رخص الباعة المتجولين')
                ->descriptionIcon('heroicon-o-shopping-cart')
                ->color('info')
                ->chart([$streetVendorCount, $totalLicensesCount])
                ->extraAttributes(['class' => 'col-span-1']),

            Stat::make('شركة قابضة', $holdingCompanyCount)
                ->description('عدد رخص الشركات القابضة')
                ->descriptionIcon('heroicon-o-home')
                ->color('info')
                ->chart([$holdingCompanyCount, $totalLicensesCount])
                ->extraAttributes(['class' => 'col-span-1']),
        ];
    }

    protected function getColumns(): int
    {
        return 4; // الشبكة تتكون من 4 أعمدة
    }
}
