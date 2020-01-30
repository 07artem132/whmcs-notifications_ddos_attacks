<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 02.12.2019, 15:48
 *
 */

use WHMCS\Module\Addon\NotificationsDDoSAttacks\Configs\ModuleConfig;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Controllers\ApiController;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Controllers\InstallController;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Controllers\PageController;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Controllers\UninstallController;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Menu\AdminAreaMenu;
use WHMCS\Service\Service;

function NotificationsDDoSAttacks_config()
{
    $configarray = [
        "name" => "Парсинг email уведомлений о ддос атаках",
        "description" => "",
        "version" => "1",
        "author" => "service-voice",
        "fields" => [
            "DeleteTableWhenDisabled" => [
                "FriendlyName" => "Удалять данные модуля при отключении ?",
                "Type" => "yesno",
                "Description" => " Отметьте здесь дабы удалить данные модуля при отключении оного.",
            ]
        ]
    ];

    return $configarray;
}

function NotificationsDDoSAttacks_output($vars)
{
    if ($_REQUEST['ajax'] === 'true') {
        $api = new ApiController();
        $api->run();
        die();
    }

    $PageController = new PageController($vars);
    $PageController->setDefaultAction('index');
    $PageController->setSuffixTemplate('admin');

    $PageController->setMenuTemplate('include\navbar.tpl');
    $PageController->setMenu((new AdminAreaMenu())->navbar());
    $PageController->run();

}

function NotificationsDDoSAttacks_clientarea($vars)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $api = new ApiController();
        $api->run();
        die();
    }

    return array(
        'pagetitle' => 'Информация о DDoS атаках',
        'breadcrumb' => array('index.php?m=NotificationsDDoSAttacks' => 'Информация о DDoS атаках'),
        'templatefile' => 'templates/client_index',
        'requirelogin' => true,
        'forcessl' => false,
        'vars' => array(
            'userid' => $_SESSION['uid'],
            'sign' => sha1($_SESSION['uid'] . ModuleConfig::getSecret()),
        ),
    );
}

function NotificationsDDoSAttacks_activate()
{
    if (!empty($error = InstallController::createTableEmailParse())) {
        return $error;
    }

    if (!empty($error = InstallController::createTableNotify())) {
        return $error;
    }

    if (!empty($error = InstallController::createTableSettings())) {
        return $error;
    }

    if (!empty($error = InstallController::createTableUserNotifyStatus())) {
        return $error;
    }

    return array(
        'status' => 'success',
        'description' => 'Модуль успешно активирован',
    );
}

function NotificationsDDoSAttacks_deactivate()
{
    if (!empty($dropTable = ModuleConfig::getModuleSetting('DeleteTableWhenDisabled'))) {
        if ($dropTable === 'on') {
            if (!empty($error = UninstallController::dropTable('mod_addon_notifications_ddos_attack_settings'))) {
                return $error;
            }

            if (!empty($error = UninstallController::dropTable('mod_addon_notifications_ddos_attack_email_parse'))) {
                return $error;
            }

            if (!empty($error = UninstallController::dropTable('mod_addon_notifications_ddos_attack_notify'))) {
                return $error;
            }

            if (!empty($error = UninstallController::dropTable('mod_addon_notifications_ddos_attack_user_notify_status'))) {
                return $error;
            }
        }
    }

    return array(
        'status' => 'success',
        'description' => 'Модуль успешно деактивирован',
    );
}



