<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 09.12.2019, 18:14
 *
 */

namespace WHMCS\Module\Addon\NotificationsDDoSAttacks\Abstracts;

use WHMCS\Module\Addon\NotificationsDDoSAttacks\Configs\ModuleConfig;

abstract class ApiAccessController
{
    public static function verifySignature(array $param, string $sign): bool
    {
        $sha1 = sha1(implode("", $param) . ModuleConfig::getSecret());

        if (strcmp($sha1, $sign) === 0) {
            return true;
        }
        return false;
    }
}