<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 12.12.2019, 23:22
 *
 */

namespace WHMCS\Module\Addon\NotificationsDDoSAttacks\Models;

use Illuminate\Database\Query\Builder;
use WHMCS\Model\AbstractModel;

/**
 * Class UserNotifyModel
 * @package WHMCS\Module\Addon\NotificationsDDoSAttacks\Models
 * @property int $user_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @mixin Builder
 */
class UserNotifyModel extends AbstractModel
{

    protected $table = "mod_addon_notifications_ddos_attack_user_notify_status";
    protected $booleans = [];
    protected $primaryKey = 'user_id';
    public $incrementing = true;
    protected $fillable = [
        'user_id',
    ];

}