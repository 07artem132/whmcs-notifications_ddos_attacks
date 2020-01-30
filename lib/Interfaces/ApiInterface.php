<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 04.12.2019, 14:25
 *
 */

namespace WHMCS\Module\Addon\NotificationsDDoSAttacks\Interfaces;

interface  ApiInterface
{
    /**
     * @return string
     */
    function validateRequestParameters(): ?array;

    function run(): void;

    function isAuth(): bool;
}