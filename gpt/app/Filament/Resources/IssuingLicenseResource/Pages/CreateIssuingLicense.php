<?php

namespace App\Filament\Resources\IssuingLicenseResource\Pages;

use App\Filament\Resources\IssuingLicenseResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateIssuingLicense extends CreateRecord
{
    protected static string $resource = IssuingLicenseResource::class;
    protected static ?string $title = 'إصدار رخصة جديدة';

    /**
     * تعديل البيانات قبل الحفظ.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // حساب الإجمالي
        $data['discount'] = ($data['licenseDuration'] ?? 0) * ($data['licenseFee'] ?? 0);

        // حساب تاريخ انتهاء الترخيص
        if (!empty($data['licenseDuration']) && $data['licenseDuration'] > 0) {
            $data['endDate'] = now()->addYears($data['licenseDuration']);
        } else {
            $data['endDate'] = now(); // أو أي قيمة افتراضية أخرى
        }

        return $data;
    }

}
