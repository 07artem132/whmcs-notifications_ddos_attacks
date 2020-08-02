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
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Models\NotifyModel;

class AdminDeleteEmailPage implements PageInterface
{
    private $templateName = 'admin_index.tpl';
    private $vars = [];

    function __construct()
    {
        NotifyModel::withTrashed()->findOrFail($_GET['id'])->forceDelete();
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        die();
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