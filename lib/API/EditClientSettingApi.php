<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 12.12.2019, 23:13
 *
 */

namespace WHMCS\Module\Addon\NotificationsDDoSAttacks\API;

use WHMCS\Module\Addon\NotificationsDDoSAttacks\Abstracts\ApiValidatorAbstract;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Controllers\UserApiAccessController;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Interfaces\ApiInterface;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Models\SettingsModel;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Models\UserNotifyModel;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Traits\ResponseTraits;

class EditClientSettingApi extends ApiValidatorAbstract implements ApiInterface
{
    use ResponseTraits;

    function rules(): array
    {
        return [
            'client_type' => [
                'required',
                'in:user',
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
        if (array_key_exists('client_email_notify_status', $_POST)) {
            if (empty($_POST['client_email_notify_status'])) {
                UserNotifyModel::find($_POST['user_id'])->delete();
            } else {
                UserNotifyModel::updateOrCreate(
                    [
                        'user_id' => $_POST['user_id'],
                    ]
                );
            }
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