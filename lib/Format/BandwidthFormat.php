<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 04.12.2019, 14:35
 *
 */

namespace WHMCS\Module\Addon\NotificationsDDoSAttacks\Format;

class BandwidthFormat
{
    /** @var array ['suffix' => 'threshold'] */
    private const THRESHOLDS = [
        '' => 900,
        'KBit/s' => 900000,
        'MBit/s' => 900000000,
        'GBit/s' => 900000000000,
        'TBit/s' => 900000000000000,
    ];
    /** @var string */
    private const DEFAULT = '900T+';

    /**
     * @param float $value
     * @param int $precision
     * @return string
     */
    static function readable( $value,  $precision = 2)
    {
        foreach (self::THRESHOLDS as $suffix => $threshold) {
            if ($value < $threshold) {
                return self::format($value, $precision, $threshold, $suffix);
            }
        }
        return self::DEFAULT;
    }

    /**
     * @param float $value
     * @param int $precision
     * @param int $threshold
     * @param string $suffix
     * @return string
     */
    static private function format( $value,  $precision,  $threshold,  $suffix)
    {
        $formattedNumber = number_format($value / ($threshold / self::THRESHOLDS['']), $precision);
        $cleanedNumber = (strpos($formattedNumber, '.') === false)
            ? $formattedNumber
            : rtrim(rtrim($formattedNumber, '0'), '.');
        return sprintf('%s %s', $cleanedNumber, $suffix);
    }
}
