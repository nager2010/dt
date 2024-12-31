<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class IssuingLicense extends Model
{
    use HasFactory;

    /**
     * الحقول القابلة للتعبئة
     */
    protected $fillable = [
        'fullName',
        'nationalID',
        'passportOrID',
        'phoneNumber',
        'email', // الحقل الجديد
        'registrationCode', // الحقل الجديد
        'projectName',
        'positionInProject',
        'projectAddress',
        'municipality_id',
        'region_id',
        'license_type_id',
        'nearestLandmark',
        'licenseDate',
        'licenseNumber',
        'licenseDuration',
        'licenseFee',
        'discount',
        'chamberOfCommerceNumber',
        'taxNumber',
        'economicNumber',
        'endDate',
        'remainingDays',
        'status',
    ];

    /**
     * الحقول التي يتم تحويلها تلقائياً
     */
    protected $casts = [
        'id' => 'integer',
        'licenseDate' => 'date',
        'endDate' => 'date', // تحويل endDate إلى كائن Carbon
        'registrationCode' => 'array', // تمثيل رمز التسجيل كمصفوفة
    ];

    /**
     * القيم الافتراضية
     */
    protected $attributes = [
        'discount' => 0,
    ];

    /**
     * Boot method to add events
     */
    protected static function booted()
    {
        static::saving(function ($license) {
            // التحقق من وجود licenseDuration
            if ($license->licenseDuration) {
                // حساب endDate إذا كانت licenseDate موجودة
                if ($license->licenseDate) {
                    $license->endDate = \Carbon\Carbon::parse($license->licenseDate)
                        ->addYears($license->licenseDuration)
                        ->toDateString();
                } else {
                    // حساب endDate بناءً على التاريخ الحالي إذا لم تكن licenseDate موجودة
                    $license->endDate = Carbon::now()
                        ->addYears($license->licenseDuration)
                        ->toDateString();
                }
            }

            // حساب الخصم إذا كانت licenseDuration و licenseFee موجودة وصحيحة
            if (isset($license->licenseDuration, $license->licenseFee) && $license->licenseFee > 0) {
                $license->discount = $license->licenseDuration * $license->licenseFee;
            } else {
                $license->discount = 0; // تعيين قيمة افتراضية للخصم
            }

            // تحديث remainingDays بناءً على endDate
            if ($license->endDate) {
                $license->remainingDays = \Carbon\Carbon::parse($license->endDate)
                    ->diffInDays(now(), false);
            } else {
                $license->remainingDays = null; // إذا لم تكن endDate محددة
            }

            // تحديث الحالة بناءً على remainingDays
            if ($license->remainingDays !== null) {
                if ($license->remainingDays < 0) {
                    $license->status = 'منتهية'; // إذا انتهت الصلاحية
                } else {
                    $license->status = 'سارية'; // إذا كانت الرخصة صالحة
                }
            } else {
                $license->status = 'قيد الإجراء'; // إذا لم تكن remainingDays محسوبة
            }
        });
    }
    public function municipality(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Municipality::class, 'municipality_id');
    }

    public function region(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }




}
