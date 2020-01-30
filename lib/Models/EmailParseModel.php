<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 04.12.2019, 14:18
 *
 */

namespace WHMCS\Module\Addon\NotificationsDDoSAttacks\Models;

use WHMCS\Model\AbstractModel;

/**
 * Class NotifyQueueModel
 * @package WHMCS\Module\Addon\NotificationsDDoSAttacks\Models
 * @property int $id
 * @property int email_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @mixin \Illuminate\Database\Query\Builder
 */
class EmailParseModel extends AbstractModel
{
    protected $table = "mod_addon_notifications_ddos_attack_email_parse";
    protected $booleans = [];
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $fillable = [
        'email_id',
    ];

}