<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 09.12.2019, 18:15
 *
 */

namespace WHMCS\Module\Addon\NotificationsDDoSAttacks\Controllers;

use Exception;
use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Abstracts\ApiAccessController;
use WHMCS\Service\Service;

class UserApiAccessController extends ApiAccessController
{
    /**
     * @param int $user_id
     * @param int $service_id
     * @return bool
     * @throws Exception
     */
    public static function AllowUserActionService(int $user_id, int $service_id): bool
    {
        $service = Service::find($service_id);

        if (empty($service)) {
            return false;
        }

        if ($service->userid !== $user_id) {
            return false;
        }

        return true;
    }

    public static function AllowAdminActionService(int $admin_id): bool
    {
        $admin = Capsule::table("tbladmins")->find($admin_id);

        if (empty($admin)) {
            return false;
        }

        return true;
    }
}