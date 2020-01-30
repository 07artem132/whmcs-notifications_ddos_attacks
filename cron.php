<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 03.12.2019, 18:46
 *
 */
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Controllers\CronController;

require __DIR__ . '/../../../init.php';

$cron = new CronController();
$cron->runTasks();