<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 09.12.2019, 18:14
 *
 */

namespace WHMCS\Module\Addon\NotificationsDDoSAttacks\API;

use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Abstracts\ApiValidatorAbstract;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Controllers\DataTablesDatabaseIntegrationController;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Controllers\UserApiAccessController;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Interfaces\ApiInterface;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Traits\ResponseTraits;

class GetNotifyApi extends ApiValidatorAbstract implements ApiInterface
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
        $columns = [
            [
                'db' => 'id', 'dt' => 'id'
            ],
            [
                'db' => 'ip', 'dt' => 'ip'
            ],
            [
                'db' => 'proto', 'dt' => 'proto'
            ],
            [
                'db' => 'bits', 'dt' => 'bits'
            ],
            [
                'db' => 'packets', 'dt' => 'packets'
            ],
            [
                'db' => 'start', 'dt' => 'start'
            ],
            [
                'db' => 'end', 'dt' => 'end'
            ],
        ];
        $this->responseRawData(json_encode( DataTablesDatabaseIntegrationController::simple($_POST, Capsule::connection()->getPdo(), 'mod_addon_notifications_ddos_attack_notify', 'id', $columns)));
    }

    /**
     * @return bool
     * @throws \Exception
     */
    function isAuth(): bool
    {
        switch ($_REQUEST['client_type']) {
            case 'user':
                return UserApiAccessController::AllowUserActionService($_REQUEST['user_id'], $_REQUEST['service_id']);
                break;
            case 'admin':
                return UserApiAccessController::AllowAdminActionService($_REQUEST['user_id']);
                break;
            default:
                return false;
        }
    }

}