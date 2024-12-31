<?php

namespace App\Filament\Resources\ExpiredLicensesResource\Pages;

use App\Filament\Resources\ExpiredLicensesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExpiredLicenses extends ListRecords
{
    protected static string $resource = ExpiredLicensesResource::class;
    protected static ?string $title = 'التراخيص المنتهية';

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
