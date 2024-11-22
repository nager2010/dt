<?php

namespace App\Filament\Resources\IssuingLicenseResource\Pages;

use App\Filament\Resources\IssuingLicenseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIssuingLicense extends EditRecord
{
    protected static string $resource = IssuingLicenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * تعديل البيانات قبل الحفظ.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // حساب الإجمالي
        $data['discount'] = ($data['licenseDuration'] ?? 0) * ($data['licenseFee'] ?? 0);

        // حساب تاريخ انتهاء الترخيص
        $data['endDate'] = now()->addYears($data['licenseDuration']);

        return $data;
    }
}
