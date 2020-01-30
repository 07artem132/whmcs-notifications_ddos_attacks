<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 04.12.2019, 14:12
 *
 */

namespace WHMCS\Module\Addon\NotificationsDDoSAttacks\Menu;

use WHMCS\Module\Addon\BackupSpaceProftpd\Configs\ModuleConfig;
use WHMCS\View\Menu\MenuFactory;

class AdminAreaMenu extends MenuFactory
{
    protected $rootItemName = "NotificationsDDoSAttacks Manager nav bar";

    public function navbar()
    {
        return $this->loader->load($this->buildMenuStructure($this->getNavBarStructure()));
    }

    protected function getNavBarStructure()
    {
        $menuItems = [
            [
                "name" => "index",
                "label" => 'Список уведомлений',
                "uri" => ModuleConfig::getModuleLink() . "&action=index",
                "order" => 0,
                "attributes" => [
                    "class" => !array_key_exists('action', $_GET) || $_GET['action'] === 'index' ? 'active' : ''
                ]
            ],
            [
                "name" => "settings",
                "label" => 'Настройки',
                "uri" => ModuleConfig::getModuleLink() . "&action=settings",
                "order" => 1,
                "attributes" => [
                    "class" => $_GET['action'] === 'settings' ? 'active' : ''
                ]
            ]
        ];

        return $menuItems;
    }

}


