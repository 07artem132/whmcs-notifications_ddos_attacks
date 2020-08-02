<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 04.12.2019, 15:31
 *
 */

namespace WHMCS\Module\Addon\NotificationsDDoSAttacks\Parser;

use Carbon\Carbon;
use PhpImap\IncomingMail;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Format\BandwidthFormat;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Format\PacketFormat;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Interfaces\NotifyDDoSAttackInterface;
use WHMCS\Service\Service;

class DDosGuardParser implements NotifyDDoSAttackInterface
{
    /**
     * @var IncomingMail
     */
    private $mail;
    private $attacks;

    function __construct($mail)
    {
        $this->mail = $mail;
    }

    private function getIP($attack)
    {
        return $attack->ip;
    }

    private function getProto($attack)
    {
        return $attack->protocolText;
    }

    private function getStart($attack)
    {
        return $attack->startTime;
    }

    private function getEnd($attack)
    {
        return $attack->endTime;
    }

    private function getBandwidth($attack)
    {
        return BandwidthFormat::readable($attack->peakSize);
    }

    private function getPackets($attack)
    {
        return PacketFormat::readable($attack->peakCount);
    }

    private function getServiceID($attack)
    {
        $serviceId = 0;

        $service = Service::where(function ($result) use ($attack) {
            $result->where('dedicatedip', '=', $this->getIP($attack))
                ->orWhere('assignedips', 'LIKE', '%' . $this->getIP($attack) . '%');
        })
            ->first();

        if (!empty($service) && Carbon::parse($service->regdate) < Carbon::parse($this->getStart($attack))) {
            $serviceId = $service->id;
        }

        return $serviceId;
    }

    function getRawEmail()
    {
        return $this->mail;
    }

    private function parseAttacks()
    {
        $attachment = collect($this->mail->getAttachments())->where('name', 'info')->first();
        $rawAttackInfo = json_decode(file_get_contents($attachment->filePath));
        foreach ($rawAttackInfo->child as $attack) {
            if ($attack->protocolText == '---') continue;
            $this->attacks[] = [
                'ip' => $this->getIP($attack),
                'proto' => $this->getProto($attack),
                'start' => $this->getStart($attack),
                'end' => $this->getEnd($attack),
                'bits' => $this->getBandwidth($attack),
                'packets' => $this->getPackets($attack),
                'service_id' => $this->getServiceID($attack),
            ];
        }
    }

    function getAttacks()
    {
        if (empty($this->attacks)) {
            $this->parseAttacks();
        }

        return $this->attacks;
    }
}