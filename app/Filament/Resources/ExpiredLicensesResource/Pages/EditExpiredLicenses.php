<?php

namespace App\Filament\Resources\ExpiredLicensesResource\Pages;

use App\Filament\Resources\ExpiredLicensesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExpiredLicenses extends EditRecord
{
    protected static string $resource = ExpiredLicensesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
