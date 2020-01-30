<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 04.12.2019, 14:20
 *
 */

namespace WHMCS\Module\Addon\NotificationsDDoSAttacks\Controllers;

use WHMCS\Database\Capsule;

class InstallController
{
    public static function createTableSettings()
    {
        try {
            if (!Capsule::schema()->hasTable('mod_addon_notifications_ddos_attack_settings')) {
                Capsule::schema()->create('mod_addon_notifications_ddos_attack_settings', function ($table) {
                    /** @var \Illuminate\Database\Schema\Blueprint $table */
                    $table->increments('id');
                    $table->string('key');
                    $table->text('val');
                    $table->timestamps();
                });
            }
        } catch (\Exception $e) {
            return array(
                'status' => 'error',
                'description' => 'При создании таблицы (mod_addon_notifications_ddos_attack_settings) возникла ошибка:' . $e->getMessage()
            );
        }
        return [];
    }

    public static function createTableUserNotifyStatus()
    {
        try {
            if (!Capsule::schema()->hasTable('mod_addon_notifications_ddos_attack_user_notify_status')) {
                Capsule::schema()->create('mod_addon_notifications_ddos_attack_user_notify_status', function ($table) {
                    /** @var \Illuminate\Database\Schema\Blueprint $table */
                    $table->unsignedInteger('user_id');
                    $table->timestamps();
                    $table->primary('user_id','user_notify_primary');
                });
            }
        } catch (\Exception $e) {
            return array(
                'status' => 'error',
                'description' => 'При создании таблицы (mod_addon_notifications_ddos_attack_user_notify_status) возникла ошибка:' . $e->getMessage()
            );
        }
        return [];
    }

    public static function createTableEmailParse()
    {
        try {
            if (!Capsule::schema()->hasTable('mod_addon_notifications_ddos_attack_email_parse')) {
                Capsule::schema()->create('mod_addon_notifications_ddos_attack_email_parse', function ($table) {
                    /** @var \Illuminate\Database\Schema\Blueprint $table */
                    $table->unsignedInteger('email_id');
                    $table->timestamps();
                    $table->primary('email_id','email_parsed_primary');
                });
            }
        } catch (\Exception $e) {
            return array(
                'status' => 'error',
                'description' => 'При создании таблицы (mod_addon_notifications_ddos_attack_email_parse) возникла ошибка:' . $e->getMessage()
            );
        }
        return [];
    }

    public static function createTableNotify()
    {
        try {
            if (!Capsule::schema()->hasTable('mod_addon_notifications_ddos_attack_notify')) {
                Capsule::schema()->create('mod_addon_notifications_ddos_attack_notify', function ($table) {
                    /** @var \Illuminate\Database\Schema\Blueprint $table */
                    $table->increments('id');
                    $table->unsignedInteger('service_id');
                    $table->string('ip');
                    $table->string('proto');
                    $table->string('bits');
                    $table->string('packets');
                    $table->timestamp('start');
                    $table->timestamp('end');
                    $table->softDeletes();
                    $table->timestamps();
                });
            }
        } catch (\Exception $e) {
            return array(
                'status' => 'error',
                'description' => 'При создании таблицы (mod_addon_notifications_ddos_attack_notify) возникла ошибка:' . $e->getMessage()
            );
        }
        return [];
    }

}