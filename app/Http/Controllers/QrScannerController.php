<?php

namespace App\Http\Controllers;

use App\Models\IssuingLicense;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class QrScannerController extends Controller
{
    public function showScanner()
    {
        return view('filament.pages.qr-scanner');
    }

    public function verifyLicense(Request $request)
    {
        try {
            Log::info('Received request:', $request->all()); // تسجيل الطلب الوارد

            $qrCode = $request->input('qr_code');
            
            if (!$qrCode) {
                Log::warning('No QR code provided in request');
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم توفير رمز أو رقم الرخصة'
                ]);
            }

            // البحث عن الترخيص في قاعدة البيانات
            $license = IssuingLicense::where(function($query) use ($qrCode) {
                $query->where('qr_code', $qrCode)
                      ->orWhere('license_number', $qrCode);
            })->first();
            
            Log::info('License search result:', ['license' => $license]); // تسجيل نتيجة البحث

            if (!$license) {
                return response()->json([
                    'success' => false,
                    'message' => 'الترخيص غير موجود في النظام'
                ]);
            }

            // التحقق من حالة الترخيص
            $today = Carbon::now();
            $endDate = Carbon::parse($license->end_date);
            $isExpired = $endDate->isPast();

            $response = [
                'success' => true,
                'is_expired' => $isExpired,
                'message' => $isExpired ? 'الترخيص منتهي الصلاحية' : 'الترخيص ساري المفعول',
                'license_number' => $license->license_number,
                'expiry_date' => $endDate->format('Y-m-d'),
                'redirect_url' => route('filament.app.resources.issuing-licenses.view', $license->id)
            ];

            Log::info('Sending response:', $response); // تسجيل الاستجابة
            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Error in verifyLicense:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء التحقق من الترخيص'
            ]);
        }
    }
}
