<?php

namespace App\Filament\Resources\IssuingLicenseResource\Widgets;

use App\Models\IssuingLicense;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class IssuingLicenseChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Issuing License Chart';

    protected static ?string $maxHeight = '200px';

    protected int | string | array $columnSpan = 'full';

    // الفلتر
    public ?string $filter = '3month';

    protected function getFilters(): ?array
    {
        return [
            'week' => 'الأسبوع',
            'month' => 'الشهر',
            '3month' => 'ثلاثة أشهر',
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
                    start: now()->subWeek(),
                    end: now(),
                )
                ->perDay()
                ->count(),
            'month' => Trend::model(IssuingLicense::class)
                ->between(
                    start: now()->subMonth(),
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
        };

        // إعداد البيانات للإرجاع
        return [
            'datasets' => [
                [
                    'label' => 'التراخيص',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
