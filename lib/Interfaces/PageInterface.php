<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 04.12.2019, 14:19
 *
 */
namespace WHMCS\Module\Addon\NotificationsDDoSAttacks\Interfaces;

interface  PageInterface
{
    /**
     * @return string
     */
    public function getTemplateName();

    /**
     * @return array
     */
    public function getVars();

    public function getSubMenu();

    public function loadJS();
}