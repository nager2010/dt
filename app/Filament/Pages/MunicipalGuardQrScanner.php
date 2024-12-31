<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\IssuingLicense;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class MunicipalGuardQrScanner extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationLabel = 'فحص الرخص';
    protected static ?string $title = 'فحص الرخص عبر QR';
    protected static ?string $slug = 'qr-scanner';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.municipal-guard-qr-scanner';

    public ?string $licenseNumber = null;
    public ?IssuingLicense $license = null;

    public static function canAccess(): bool
    {
        return auth()->user()->can('scan_licenses');
    }

    public function mount(): void
    {
        if (!static::canAccess()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }
    }

    public function searchLicense(): void
    {
        $this->validate([
            'licenseNumber' => ['required', 'string'],
        ], [
            'licenseNumber.required' => 'رقم الرخصة مطلوب',
            'licenseNumber.string' => 'رقم الرخصة يجب أن يكون نصاً'
        ]);

        $this->license = IssuingLicense::where('licenseNumber', $this->licenseNumber)->first();

        if (!$this->license) {
            $this->addError('licenseNumber', 'لم يتم العثور على الرخصة');
            return;
        }
    }

    public static function getNavigationGroup(): ?string
    {
        return 'إدارة الرخص';
    }

    public function render(): View
    {
        return view('filament.pages.municipal-guard-qr-scanner', [
            'license' => $this->license,
        ]);
    }
}
