<?php

namespace App\Filament\Resources\RegionResource\Pages;

use App\Filament\Resources\RegionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRegions extends ListRecords
{
    protected static string $resource = RegionResource::class;
    protected static ?string $title = 'المحلات';

    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make()
//            ->label('إضافة محلة جديدة'),
        ];
    }
}
