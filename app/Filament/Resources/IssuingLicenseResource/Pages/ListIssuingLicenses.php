<?php

namespace App\Filament\Resources\IssuingLicenseResource\Pages;

use App\Filament\Resources\IssuingLicenseResource;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;

class ListIssuingLicenses extends ListRecords
{
    use ExposesTableToWidgets;
    protected static string $resource = IssuingLicenseResource::class;

    protected static ?string $title = 'إصدار التراخيص';

    protected function getHeaderWidgets(): array
    {
        return [
//            IssuingLicenseStateWidget::class,
//            IssuingLicenseChartWidget::class,
        ];
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('إصدار ترخيص جديد'),
        ];
    }

}
