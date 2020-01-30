<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 13.12.2019, 23:41
 *
 */

use WHMCS\Module\Addon\NotificationsDDoSAttacks\Models\NotifyModel;

add_hook('ServiceDelete', 1, function ($vars) {
    foreach (NotifyModel::where('service_id', '=', $vars['params']['serviceid'])->get() as $notifyModel) {
        $notifyModel->service_id = 0;
        $notifyModel->saveOrFail();
        $notifyModel->delete();
    }
});
