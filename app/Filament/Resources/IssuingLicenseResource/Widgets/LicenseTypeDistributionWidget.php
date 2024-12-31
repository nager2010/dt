<?php

namespace App\Filament\Resources\IssuingLicenseResource\Widgets;

use App\Models\IssuingLicense;
use Filament\Widgets\ChartWidget;

class LicenseTypeDistributionWidget extends ChartWidget
{
    protected static ?string $heading = 'توزيع أنواع الرخص';
    protected static ?string $maxHeight = '300px';
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $types = [
            'company' => 'الشركات',
            'individual' => 'الأفراد',
            'commercial' => 'تجاري',
            'industrial' => 'صناعي',
            'craft_service' => 'خدمات حرفية',
            'professional_service' => 'خدمات مهنية',
            'general' => 'عام',
            'street_vendor' => 'بائع متجول',
            'holding_company' => 'شركة قابضة',
        ];

        $data = [];
        $labels = [];
        $backgroundColor = [];  
        $colors = [
            '#3B82F6', // blue
            '#F59E0B', // amber
            '#10B981', // emerald
            '#6366F1', // indigo
            '#EC4899', // pink
            '#8B5CF6', // purple
            '#64748B', // slate
            '#EF4444', // red
            '#14B8A6', // teal
        ];

        $i = 0;
        foreach ($types as $type => $label) {
            $count = IssuingLicense::where('license_type_id', $type)->count();
            if ($count > 0) {
                $data[] = $count;
                $labels[] = $label . ' (' . $count . ')';  
                $backgroundColor[] = $colors[$i % count($colors)];  
                $i++;
            }
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $backgroundColor,
                ]
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
