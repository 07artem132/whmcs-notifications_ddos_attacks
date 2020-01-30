<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 09.12.2019, 17:51
 *
 */

namespace WHMCS\Module\Addon\NotificationsDDoSAttacks\Format;


class WanguardBandwidth
{
    private const CONVERT = [
        'K' => ' KBit/s',
        'M' => ' MBit/s',
        'G' => ' GBit/s',
        'T' => ' TBit/s',
    ];

    /**
     * @param $value
     * @param int $precision
     * @return string formatted string
     */
    public static function readable($value, $precision = 2)
    {
        if (array_key_exists($value[-1], self::CONVERT)) {
            $suffix = self::CONVERT[$value[-1]];
        } else {
            $suffix = '';
        }

        $number = (float)filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT);
        return number_format($number, $precision). $suffix;
    }
}