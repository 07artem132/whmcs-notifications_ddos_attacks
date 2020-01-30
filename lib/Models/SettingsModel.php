<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 04.12.2019, 14:16
 *
 */
 namespace WHMCS\Module\Addon\NotificationsDDoSAttacks\Models;

use Illuminate\Database\Eloquent\Builder;
use WHMCS\Model\AbstractModel;

/**
 * Class SettingsModel
 * @package WHMCS\Module\Addon\NotificationsDDoSAttacks\Models
 * @property string $key
 * @property string $val
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @mixin Builder
 */
class SettingsModel extends AbstractModel
{
    protected $table = "mod_addon_notifications_ddos_attack_settings";
    protected $booleans = [];
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $fillable = [
        'key',
        'val'
    ];

}