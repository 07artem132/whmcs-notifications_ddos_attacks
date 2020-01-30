<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 04.12.2019, 14:21
 *
 */

namespace WHMCS\Module\Addon\NotificationsDDoSAttacks\Controllers;

use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Configs\ModuleConfig;

class UninstallController
{

    public static function dropTable($tableName)
    {
        try {
            Capsule::schema()->dropIfExists($tableName);
        } catch (\Exception $e) {
            return array(
                'status' => 'error',
                'description' => sprintf('При удалении таблицы %s произошла ошибка: %s', $tableName, $e->getMessage())
            );
        }

        return [];
    }

}