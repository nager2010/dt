<?php
use App\Http\Controllers\ExternalLicenseRequestController;
use App\Http\Controllers\QrScannerController;
use Illuminate\Support\Facades\Route;

// مسارات طلب الرخصة الخارجية
Route::get('/license-request', [ExternalLicenseRequestController::class, 'create'])->name('license-requests.create');
Route::post('/license-request', [ExternalLicenseRequestController::class, 'store'])->name('license-requests.store');
Route::get('/license-request/success', [ExternalLicenseRequestController::class, 'success'])->name('license-requests.success');
Route::get('/regions/{municipality}', [ExternalLicenseRequestController::class, 'getRegions']);

// QR Scanner Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/scanner', [QrScannerController::class, 'showScanner'])->name('scanner');
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/verify-license', [QrScannerController::class, 'verifyLicense'])->name('verify.license');
});
