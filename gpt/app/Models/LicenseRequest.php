<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'fullName',
        'nationalID',
        'passportOrID',
        'phoneNumber',
        'email',
        'registrationCode',
        'projectName',
        'positionInProject',
        'projectAddress',
        'municipality_id',
        'region_id',
        'license_type_id',
        'nearestLandmark',
        'licenseFee',
        'licenseDuration',
        'licenseDate',
        'chamberOfCommerceNumber',
        'taxNumber',
        'economicNumber',
        'admin_note',
        'status',
    ];

    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipality_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

}
