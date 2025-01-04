<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\View\View;

class QrScanner extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.qr-scanner';

    // إخفاء Navbar
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    // إخفاء Header
    public static function shouldShowHeader(): bool
    {
        return false;
    }
}
