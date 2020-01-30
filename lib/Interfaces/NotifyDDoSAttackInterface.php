<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 05.12.2019, 19:46
 *
 */


namespace WHMCS\Module\Addon\NotificationsDDoSAttacks\Interfaces;

interface  NotifyDDoSAttackInterface
{
    function getRawEmail();

    function getAttacks();
}
