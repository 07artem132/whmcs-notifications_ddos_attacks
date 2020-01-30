<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 12.12.2019, 13:23
 *
 */


namespace WHMCS\Module\Addon\NotificationsDDoSAttacks\API;

use WHMCS\Module\Addon\NotificationsDDoSAttacks\Abstracts\ApiValidatorAbstract;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Controllers\UserApiAccessController;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Interfaces\ApiInterface;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Models\SettingsModel;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Traits\ResponseTraits;

class EditAdminSettingApi extends ApiValidatorAbstract implements ApiInterface
{
    use ResponseTraits;

    function rules(): array
    {
        return [
            'client_type' => [
                'required',
                'in:admin',
            ],
            'user_id' => [
                'required',
            ],
            'sign' => [
                'required',
            ]
        ];
    }

    /**
     * @return array|null
     */
    function validateRequestParameters(): ?array
    {
        $errors = $this->validate();

        if (count($errors) !== 0) {
            return $errors;
        }

        if (!UserApiAccessController::verifySignature(
            [
                $_REQUEST['user_id'],
            ],
            $_REQUEST['sign']
        )) {
            $errors['sign'][] = 'error verify signature';
        }

        return empty($errors) ? null : $errors;
    }


    function run(): void
    {
        if (array_key_exists('ddos_guard_parsing_status', $_POST)) {
            if (empty($_POST['ddos_guard_parsing_status'])) {
               SettingsModel::updateOrCreate(
                    [
                        'key' => 'ddos_guard_parsing_status',
                    ]
                )->fill(['val' => '0'])->save();
            } else {
                SettingsModel::updateOrCreate(
                    [
                        'key' => 'ddos_guard_parsing_status',
                    ]
                )->fill(['val' => '1'])->save();
            }
        }

        if (array_key_exists('wanguard_parsing_status', $_POST)) {
            if (empty($_POST['wanguard_parsing_status'])) {
                SettingsModel::updateOrCreate(
                    [
                        'key' => 'wanguard_parsing_status',
                    ]
                )->fill(['val' => '0'])->save();
            } else {
                SettingsModel::updateOrCreate(
                    [
                        'key' => 'wanguard_parsing_status',
                    ]
                )->fill(['val' => '1'])->save();
            }
        }

        if (array_key_exists('clientNotifyEmailTemplate', $_POST)) {
            SettingsModel::updateOrCreate(
                [
                    'key' => 'clientNotifyEmailTemplate',
                ]
            )->fill(['val' => $_POST['clientNotifyEmailTemplate']])->save();
        }

        if (array_key_exists('imap_server_ip', $_POST)) {
            SettingsModel::updateOrCreate(
                [
                    'key' => 'imap_server_ip',
                ]
            )->fill(['val' => $_POST['imap_server_ip']])->save();
        }

        if (array_key_exists('imap_server_port', $_POST)) {
            SettingsModel::updateOrCreate(
                [
                    'key' => 'imap_server_port',
                ]
            )->fill(['val' => $_POST['imap_server_port']])->save();
        }

        if (array_key_exists('imap_server_login', $_POST)) {
            SettingsModel::updateOrCreate(
                [
                    'key' => 'imap_server_login',
                ]
            )->fill(['val' => $_POST['imap_server_login']])->save();
        }

        if (array_key_exists('imap_server_password', $_POST)) {
            SettingsModel::updateOrCreate(
                [
                    'key' => 'imap_server_password',
                ]
            )->fill(['val' => $_POST['imap_server_password']])->save();
        }

        if (array_key_exists('allow_save_attack', $_POST)) {
            SettingsModel::updateOrCreate(
                [
                    'key' => 'allow_save_attack',
                ]
            )->fill(['val' => $_POST['allow_save_attack']])->save();
        }

        $this->responseData('success', []);
    }


    /**
     * @return bool
     * @throws \Exception
     */
    function isAuth(): bool
    {
        switch ($_REQUEST['client_type']) {
            case 'user':
                return true;
                break;
            case 'admin':
                return UserApiAccessController::AllowAdminActionService($_REQUEST['user_id']);
                break;
            default:
                return false;
        }
    }

}