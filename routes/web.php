<?php
use App\Http\Controllers\ExternalLicenseRequestController;

//use App\Filament\Resources\IssuingLicenseResource\Pages\CreateIssuingLicense;
//
//Route::get('/issuing-licenses/create', CreateIssuingLicense::class)
//    ->name('issuing-licenses.create-public')
//    ->withoutMiddleware('auth'); // تعطيل المصادقة

// مسارات طلب الرخصة الخارجية
Route::get('/license-request', [ExternalLicenseRequestController::class, 'create'])->name('license-requests.create');
Route::post('/license-request', [ExternalLicenseRequestController::class, 'store'])->name('license-requests.store');
Route::get('/license-request/success', [ExternalLicenseRequestController::class, 'success'])->name('license-requests.success');
Route::get('/regions/{municipality}', [ExternalLicenseRequestController::class, 'getRegions']);
