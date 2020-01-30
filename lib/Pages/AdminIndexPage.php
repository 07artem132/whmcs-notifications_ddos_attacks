<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 04.12.2019, 14:12
 *
 */

namespace WHMCS\Module\Addon\NotificationsDDoSAttacks\Pages;

use WHMCS\Module\Addon\NotificationsDDoSAttacks\Configs\ModuleConfig;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Interfaces\PageInterface;

class AdminIndexPage implements PageInterface
{
    private $templateName = 'admin_index.tpl';
    private $vars = [];

    function __construct()
    {
        $this->vars['sign'] = sha1($_SESSION['adminid'] . ModuleConfig::getSecret());
        $this->vars['userid'] = $_SESSION['adminid'];
    }

    function getTemplateName()
    {
        return $this->templateName;
    }

    /**
     * @return array
     */
    function getVars()
    {
        return $this->vars;
    }

    function getSubMenu()
    {
        return null;
    }

    function loadJS()
    {
        return [
            'admin/main.js',
            'admin/notify.min.js',
        ];
    }
}