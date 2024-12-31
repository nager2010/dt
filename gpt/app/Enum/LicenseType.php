<?php

namespace App\Enum;

enum LicenseType: string
{
    case Company = 'company';
    case IndividualActivity = 'individual_activity';

    public function label(): string
    {
        return match($this) {
            self::Company => 'شركة',
            self::IndividualActivity => 'نشاط فردي',
        };
    }
}
