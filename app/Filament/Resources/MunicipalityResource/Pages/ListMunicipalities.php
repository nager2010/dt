<?php

namespace App\Filament\Resources\MunicipalityResource\Pages;

use App\Filament\Resources\MunicipalityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMunicipalities extends ListRecords
{
    protected static string $resource = MunicipalityResource::class;

    protected static ?string $title = 'البلديات';


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('إضافة بلدية جديدة'),
        ];
    }
}
