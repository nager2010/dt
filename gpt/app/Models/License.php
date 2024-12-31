<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    use HasFactory;

    // الحقول القابلة للتعبئة
    protected $fillable = [
        'name',
        'licenseDate',
        'endDate',
        'status',
    ];

    /**
     * تحديد إذا ما كانت الرخصة منتهية الصلاحية.
     */
    public function isExpired(): bool
    {
        return $this->endDate < now();
    }
}
