<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 09.12.2019, 20:23
 *
 */

namespace WHMCS\Module\Addon\NotificationsDDoSAttacks\API;

use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Abstracts\ApiValidatorAbstract;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Controllers\DataTablesDatabaseIntegrationController;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Controllers\UserApiAccessController;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Interfaces\ApiInterface;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Traits\ResponseTraits;
use WHMCS\Service\Service;

class GetClientNotifyApi extends ApiValidatorAbstract implements ApiInterface
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
        $clientServices = Service::where('userid', '=', $_SESSION['uid'])->where('domainstatus','=','Active')->get();

    /*    $mainIps = $clientServices->pluck('dedicatedip')->filter(function ($val) {
            return !empty($val);
        })->toArray();

        $assignIps = $clientServices->pluck('assignedips')->transform(function ($val) {
            return explode(PHP_EOL, $val);
        })->flatten()->filter(function ($val) {
            return !empty($val);
        })->toArray();

        $clientIps = array_merge($mainIps, $assignIps);
*/
        $columns = [
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
        $this->responseRawData(
            json_encode(
                DataTablesDatabaseIntegrationController::complex(
                    $_POST,
                    Capsule::connection()->getPdo(),
                    'mod_addon_notifications_ddos_attack_notify',
                    'id',
                    $columns,
                    null,
                   'service_id IN (\''. implode("','", $clientServices->pluck('id')->toArray()).'\') and deleted_at is null and service_id !=\'0\''
                )
            )
        );
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