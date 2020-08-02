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
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Format\WanguardBandwidth;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Interfaces\NotifyDDoSAttackInterface;
use WHMCS\Service\Service;

class WanguardParser implements NotifyDDoSAttackInterface
{
    /**
     * @var IncomingMail
     */
    private $mail;
    private $reSubject = '/Abnormal (?<type>.+?\s)traffic to\s(?<ip>.+?\s)/';
    private $reBody = '/(?:From:\s+(?<start>.+?\n))(?:.*)(?:Until:\s+(?<end>.+?\n))(?:.*)(?:Peak Total Pkts\/s:\s+(?<packets>.+?\n))(?:.*)(?:Peak Total Bits\/s:\s+(?<bits>.+?\n))/s';
    private $attacks;

    function __construct($mail)
    {
        $this->mail = $mail;
    }

    private function parseSubject()
    {
        preg_match($this->reSubject, $this->mail->subject, $matches);
        $ip = trim($matches['ip']);
        $proto = trim($matches['type']);

        return [
            'ip' => $ip,
            'proto' => $proto
        ];
    }

    private function parseBody()
    {
        preg_match($this->reBody, strip_tags($this->mail->textHtml), $matches);
        $start = str_replace(array("\n", "\r"), '', $matches['start']);
        $end = str_replace(array("\n", "\r"), '', $matches['end']);
        $packets = str_replace(array("\n", "\r"), '', $matches['packets']);
        $bits = str_replace(array("\n", "\r"), '', $matches['bits']);

        return [
            'start' => $start,
            'end' => $end,
            'packets' => $packets,
            'bits' => $bits,
        ];
    }

    private function getIP($attack)
    {
        return $attack['ip'];
    }

    private function getProto($attack)
    {
        return $attack['proto'];
    }

    private function getStart($attack)
    {
        return $attack['start'];
    }

    private function getEnd($attack)
    {
        return $attack['end'];
    }

    private function getBandwidth($attack)
    {
        return WanguardBandwidth::readable($attack['bits']);
    }

    private function getPackets($attack)
    {
        return strtoupper($attack['packets']) . ' pps';
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
        $attack = array_merge($this->parseBody(), $this->parseSubject());
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

    function getAttacks()
    {
        if (empty($this->attacks)) {
            $this->parseAttacks();
        }

        return $this->attacks;
    }
}