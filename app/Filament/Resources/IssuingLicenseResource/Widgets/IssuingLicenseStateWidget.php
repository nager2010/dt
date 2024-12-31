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
        $totalLicensesCount = IssuingLicense::count();
        
        // إحصائيات حسب نوع الترخيص
        $companyCount = IssuingLicense::where('license_type_id', 'company')->count();
        $individualCount = IssuingLicense::where('license_type_id', 'individual')->count();
        
        // إحصائيات حسب النشاط
        $commercialCount = IssuingLicense::where('license_type_id', 'commercial')->count();
        $industrialCount = IssuingLicense::where('license_type_id', 'industrial')->count();
        $craftServiceCount = IssuingLicense::where('license_type_id', 'craft_service')->count();
        $professionalServiceCount = IssuingLicense::where('license_type_id', 'professional_service')->count();
        $generalCount = IssuingLicense::where('license_type_id', 'general')->count();
        $streetVendorCount = IssuingLicense::where('license_type_id', 'street_vendor')->count();
        $holdingCompanyCount = IssuingLicense::where('license_type_id', 'holding_company')->count();

        // إحصائيات الشهر الحالي
        $currentMonthCount = IssuingLicense::whereMonth('created_at', now()->month)->count();
        $lastMonthCount = IssuingLicense::whereMonth('created_at', now()->subMonth()->month)->count();
        $monthlyGrowth = $lastMonthCount > 0 ? (($currentMonthCount - $lastMonthCount) / $lastMonthCount) * 100 : 0;

        // حساب النسب المئوية بأمان
        $companyPercentage = $totalLicensesCount > 0 ? ($companyCount / $totalLicensesCount) * 100 : 0;
        $individualPercentage = $totalLicensesCount > 0 ? ($individualCount / $totalLicensesCount) * 100 : 0;

        return [
            // إجمالي الرخص مع نسبة النمو الشهري
            Stat::make('إجمالي الرخص', number_format($totalLicensesCount))
                ->description($totalLicensesCount > 0 ? sprintf('نمو شهري %.1f%%', $monthlyGrowth) : 'لا توجد رخص')
                ->descriptionIcon($monthlyGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($monthlyGrowth >= 0 ? 'success' : 'danger')
                ->chart([$lastMonthCount, $currentMonthCount])
                ->extraAttributes(['class' => 'col-span-2']),

            // تصنيف الرخص (شركات/أفراد)
            Stat::make('رخص الشركات', number_format($companyCount))
                ->description($totalLicensesCount > 0 ? sprintf('%.1f%% من الإجمالي', $companyPercentage) : 'لا توجد رخص')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('primary')
                ->chart([$companyCount, $totalLicensesCount - $companyCount])
                ->extraAttributes(['class' => 'col-span-2']),

            Stat::make('رخص الأفراد', number_format($individualCount))
                ->description($totalLicensesCount > 0 ? sprintf('%.1f%% من الإجمالي', $individualPercentage) : 'لا توجد رخص')
                ->descriptionIcon('heroicon-m-user')
                ->color('warning')
                ->chart([$individualCount, $totalLicensesCount - $individualCount])
                ->extraAttributes(['class' => 'col-span-2']),

            // الأنشطة التجارية
            Stat::make('النشاط التجاري', number_format($commercialCount))
                ->description('رخص تجارية')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info')
                ->extraAttributes(['class' => 'col-span-1']),

            Stat::make('النشاط الصناعي', number_format($industrialCount))
                ->description('رخص صناعية')
                ->descriptionIcon('heroicon-m-cog')
                ->color('info')
                ->extraAttributes(['class' => 'col-span-1']),

            Stat::make('الخدمات الحرفية', number_format($craftServiceCount))
                ->description('رخص حرفية')
                ->descriptionIcon('heroicon-m-wrench')
                ->color('success')
                ->extraAttributes(['class' => 'col-span-1']),

            Stat::make('الخدمات المهنية', number_format($professionalServiceCount))
                ->description('رخص مهنية')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success')
                ->extraAttributes(['class' => 'col-span-1']),

            Stat::make('الرخص العامة', number_format($generalCount))
                ->description('رخص عامة')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('gray')
                ->extraAttributes(['class' => 'col-span-1']),

            Stat::make('الباعة المتجولين', number_format($streetVendorCount))
                ->description('رخص متجولين')
                ->descriptionIcon('heroicon-m-truck')
                ->color('warning')
                ->extraAttributes(['class' => 'col-span-1']),
        ];
    }

    protected function getColumns(): int
    {
        return 4; // الشبكة تتكون من 4 أعمدة
    }
}
