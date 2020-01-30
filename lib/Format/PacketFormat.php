<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 04.12.2019, 14:36
 *
 */

namespace WHMCS\Module\Addon\NotificationsDDoSAttacks\Format;

class PacketFormat
{
    /** @var array ['suffix' => 'threshold'] */
    private const THRESHOLDS = [
        '' => 900,
        'K' => 900000,
        'M' => 900000000,
        'B' => 900000000000,
        'T' => 90000000000000,
    ];
    /** @var string */
    private const DEFAULT = '900T+';

    /**
     * @param float $value
     * @param int $precision
     * @return string
     */
    static function readable( $value,  $precision = 1)
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
        return sprintf('%s%s pps', $cleanedNumber, $suffix);
    }
}
