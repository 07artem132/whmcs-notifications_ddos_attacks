<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 11.12.2019, 22:04
 *
 */


namespace WHMCS\Module\Addon\NotificationsDDoSAttacks\Pages;

use WHMCS\Mail\Template;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Configs\ModuleConfig;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Interfaces\PageInterface;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Models\SettingsModel;

class AdminSettingsPage implements PageInterface
{
    private $templateName = 'admin_setting.tpl';
    private $vars = [];

    function __construct()
    {
        $this->vars['sign'] = sha1($_SESSION['adminid'] . ModuleConfig::getSecret());
        $this->vars['userid'] = $_SESSION['adminid'];
        $this->vars['setting'] = SettingsModel::all()->keyBy('key')->transform(function ($val) {
            return $val->val;
        })->toArray();
        $this->vars['template'] = Template::where("type", "=", 'notification')
            ->where("language", "=", "")
            ->orderBy("name")
            ->get(['name', 'id'])
            ->keyBy('id')->transform(function ($item) {
                return $item->name;
            })->toArray();

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
            'admin/autoSave.js',
        ];
    }
}