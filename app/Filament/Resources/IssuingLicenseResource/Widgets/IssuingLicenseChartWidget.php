<?php

namespace App\Filament\Resources\IssuingLicenseResource\Widgets;

use App\Models\IssuingLicense;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class IssuingLicenseChartWidget extends ChartWidget
{
    protected static ?string $heading = 'إحصائيات الرخص';

    protected static ?string $maxHeight = '300px';

    protected int | string | array $columnSpan = 'full';

    // الفلتر
    public ?string $filter = '3month';

    protected function getFilters(): ?array
    {
        return [
            'week' => 'الأسبوع الحالي',
            'month' => 'الشهر الحالي',
            '3month' => 'آخر 3 أشهر',
            '6month' => 'آخر 6 أشهر',
            'year' => 'السنة الحالية',
        ];
    }

    protected function getData(): array
    {
        // ضبط الفلتر
        $filter = $this->filter;

        // بناء الاستعلام بناءً على الفلتر المختار
        $data = match($filter) {
            'week' => Trend::model(IssuingLicense::class)
                ->between(
                    start: now()->startOfWeek(),
                    end: now(),
                )
                ->perDay()
                ->count(),
            'month' => Trend::model(IssuingLicense::class)
                ->between(
                    start: now()->startOfMonth(),
                    end: now(),
                )
                ->perDay()
                ->count(),
            '3month' => Trend::model(IssuingLicense::class)
                ->between(
                    start: now()->subMonths(3),
                    end: now(),
                )
                ->perMonth()
                ->count(),
            '6month' => Trend::model(IssuingLicense::class)
                ->between(
                    start: now()->subMonths(6),
                    end: now(),
                )
                ->perMonth()
                ->count(),
            'year' => Trend::model(IssuingLicense::class)
                ->between(
                    start: now()->startOfYear(),
                    end: now(),
                )
                ->perMonth()
                ->count(),
        };

        // إعداد البيانات للإرجاع
        return [
            'datasets' => [
                [
                    'label' => 'عدد الرخص',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'fill' => true,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'tension' => 0.3,
                ]
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
