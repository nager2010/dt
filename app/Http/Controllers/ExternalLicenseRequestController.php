<?php

namespace App\Http\Controllers;

use App\Models\LicenseRequest;
use App\Models\Municipality;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ExternalLicenseRequestController extends Controller
{
    public function __construct()
    {
        // إضافة headers لمنع التخزين المؤقت والسماح بالإطارات
        $this->middleware(function ($request, $next) {
            $response = $next($request);
            return $response->header('X-Frame-Options', 'SAMEORIGIN')
                          ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                          ->header('Pragma', 'no-cache')
                          ->header('Expires', '0');
        });
    }

    public function create()
    {
        $municipalities = Municipality::pluck('name', 'id');
        $licenseTypes = [
            'commercial' => 'تجاري',
            'industrial' => 'صناعي',
            'craft_service' => 'حرفي خدمي',
            'professional_service' => 'خدمي مهني',
            'general' => 'عام',
            'street_vendor' => 'بائع متجول',
            'holding_company' => 'شركة قابضة',
        ];
        
        return view('license-requests.external-create', compact('municipalities', 'licenseTypes'));
    }

    public function getRegions($municipalityId)
    {
        Log::info('Fetching regions for municipality: ' . $municipalityId);
        
        try {
            $regions = Region::where('municipality_id', $municipalityId)->get();
            Log::info('Found regions: ' . $regions->count());
            
            $formattedRegions = $regions->pluck('name', 'id')->toArray();
            Log::info('Formatted regions: ', $formattedRegions);
            
            return response()->json($formattedRegions);
        } catch (\Exception $e) {
            Log::error('Error fetching regions: ' . $e->getMessage());
            return response()->json(['error' => 'حدث خطأ في جلب المحلات: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        Log::info('Received license request data:', $request->all());

        try {
            $validator = Validator::make($request->all(), [
                'fullName' => 'required|string|max:120',
                'nationalID' => 'required|string|max:20',
                'passportOrID' => 'nullable|string|max:20',
                'phoneNumber' => 'required|string|max:15',
                'email' => 'nullable|email|max:120',
                'projectName' => ['required', 'string', 'max:120'],
                'positionInProject' => 'required|in:owner,general_manager,chairman',
                'projectAddress' => 'required|string|max:160',
                'municipality_id' => 'required|exists:municipalities,id',
                'region_id' => 'required|exists:regions,id',
                'license_type_id' => 'required',
                'nearestLandmark' => 'required|string|max:180',
            ], [
                'required' => 'حقل :attribute مطلوب',
                'string' => 'حقل :attribute يجب أن يكون نصاً',
                'max' => 'حقل :attribute يجب أن لا يتجاوز :max حرف',
                'email' => 'حقل :attribute يجب أن يكون بريد إلكتروني صحيح',
                'exists' => 'القيمة المحددة في حقل :attribute غير صالحة',
                'in' => 'القيمة المحددة في حقل :attribute غير صالحة',
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed:', $validator->errors()->toArray());
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();
            
            // تعيين القيم الافتراضية
            $now = Carbon::now();
            
            $data['registrationCode'] = '1010' . str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
            $data['licenseDate'] = $now;
            $data['licenseFee'] = 500;
            $data['licenseDuration'] = 1;
            $data['endDate'] = $now->copy()->addYear();
            $data['status'] = 'Pending';

            Log::info('Creating license request with data:', $data);
            
            $licenseRequest = LicenseRequest::create($data);
            Log::info('License request created successfully with ID: ' . $licenseRequest->id);
            
            return response()->json([
                'success' => true,
                'message' => 'تم تقديم الطلب بنجاح',
                'redirect' => 'https://baldy.masarfezzan.com/'
            ])->header('X-Frame-Options', 'SAMEORIGIN');
        } catch (\Exception $e) {
            Log::error('Error creating license request: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'حدث خطأ في حفظ الطلب',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function success()
    {
        return view('license-requests.success');
    }
}
