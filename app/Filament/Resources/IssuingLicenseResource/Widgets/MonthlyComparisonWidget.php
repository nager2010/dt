<?php

namespace App\Filament\Resources\IssuingLicenseResource\Widgets;

use App\Models\IssuingLicense;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class MonthlyComparisonWidget extends ChartWidget
{
    protected static ?string $heading = 'إحصائيات الرخص الشهرية';
    protected static ?string $maxHeight = '300px';
    protected int | string | array $columnSpan = 'full';

    public ?string $filter = 'year';

    protected function getFilters(): ?array
    {
        return [
            'year' => 'السنة الحالية',
            'custom' => 'آخر 12 شهر',
        ];
    }

    protected function getData(): array
    {
        if ($this->filter === 'year') {
            $startDate = Carbon::now()->startOfYear();
            $endDate = Carbon::now();
        } else {
            $startDate = Carbon::now()->subMonths(11)->startOfMonth();
            $endDate = Carbon::now();
        }

        $currentDate = clone $startDate;
        $arabicMonths = [
            'يناير', 'فبراير', 'مارس', 'إبريل', 'مايو', 'يونيو',
            'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
        ];

        $data = [];
        $labels = [];
        $totalCount = 0;

        while ($currentDate <= $endDate) {
            $month = $currentDate->month;
            $year = $currentDate->year;

            $monthlyCount = IssuingLicense::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();

            $data[] = $monthlyCount;
            $totalCount += $monthlyCount;
            
            // استخدام الأسماء العربية للأشهر مع عدد الرخص
            $labels[] = sprintf('%s %d (%d)', $arabicMonths[$month - 1], $year, $monthlyCount);

            $currentDate->addMonth();
        }

        return [
            'datasets' => [
                [
                    'label' => 'عدد الرخص',
                    'data' => $data,
                    'backgroundColor' => '#3B82F6',
                    'borderColor' => '#2563EB',
                    'borderWidth' => 1,
                ]
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
