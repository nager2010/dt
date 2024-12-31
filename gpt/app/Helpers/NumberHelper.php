<?php

namespace App\Helpers;

use NumberToWords\NumberToWords;

class NumberHelper
{
    /**
     * تحويل الرقم إلى نص باللغة العربية.
     *
     * @param int|float $number
     * @return string
     */
    public static function numberToWords($number): string
    {
        $numberToWords = new NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer('ar'); // 'ar' للغة العربية
        return $numberTransformer->toWords($number);
    }

    /**
     * صياغة السنوات بناءً على العدد.
     *
     * @param int $years
     * @return string
     */
    public static function yearsToWords($years): string
    {
        if ($years == 1) {
            return 'سنة واحدة';
        } elseif ($years == 2) {
            return 'سنتان';
        } elseif ($years >= 3 && $years <= 10) {
            return self::numberToWords($years) . ' سنوات';
        } else {
            return self::numberToWords($years) . ' سنة';
        }
    }

    /**
     * تحويل القيمة المالية إلى نص باللغة العربية مع العملة.
     *
     * @param float $amount
     * @return string
     */
    public static function amountToWords($amount): string
    {
        $integerPart = (int)$amount; // الجزء الصحيح
        $fractionalPart = round(($amount - $integerPart) * 100); // الجزء الكسري (للقروش)

        $words = self::numberToWords($integerPart) . ' دينار'; // الجزء الصحيح بالنص

        if ($fractionalPart > 0) {
            $words .= ' و' . self::numberToWords($fractionalPart) . ' درهم'; // إضافة الجزء الكسري
        }

        return $words;
    }
}
