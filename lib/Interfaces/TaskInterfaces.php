<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 03.12.2019, 18:49
 *
 */

namespace WHMCS\Module\Addon\NotificationsDDoSAttacks\Interfaces;


interface TaskInterfaces
{
    public function getName(): string;

    public function run(): void;

    public function getFrequency(): string;

}