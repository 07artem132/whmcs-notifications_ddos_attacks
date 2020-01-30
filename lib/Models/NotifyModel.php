<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 04.12.2019, 14:16
 *
 */

namespace WHMCS\Module\Addon\NotificationsDDoSAttacks\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use WHMCS\Model\AbstractModel;

/**
 * Class NotifyQueueModel
 * @package WHMCS\Module\Addon\NotificationsDDoSAttacks\Models
 * @property int $id
 * @property int $service_id
 * @property string ip
 * @property string proto
 * @property string bits
 * @property string packets
 * @property \Carbon\Carbon start
 * @property \Carbon\Carbon end
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @mixin \Illuminate\Database\Query\Builder
 */
class NotifyModel extends AbstractModel
{
    use SoftDeletes;

    protected $table = "mod_addon_notifications_ddos_attack_notify";
    protected $booleans = [];
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $fillable = [
        'service_id',
        'ip',
        'proto',
        'bits',
        'packets',
        'start',
        'end',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function service()
    {
        return $this->hasOne("WHMCS\\Service\\Service", 'id', 'service_id');
    }

}