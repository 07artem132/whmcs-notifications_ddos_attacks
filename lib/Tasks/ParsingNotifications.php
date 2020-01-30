<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 03.12.2019, 18:56
 *
 */

namespace WHMCS\Module\Addon\NotificationsDDoSAttacks\Tasks;

use Carbon\Carbon;
use Illuminate\Database\QueryException;
use PhpImap\Exception;
use PhpImap\Mailbox;
use WHMCS\Database\Capsule;
use WHMCS\Mail\Template;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Interfaces\NotifyDDoSAttackInterface;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Interfaces\TaskInterfaces;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Models\EmailParseModel;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Models\NotifyModel;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Models\SettingsModel;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Models\UserNotifyModel;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Parser\DDosGuardParser;
use WHMCS\Module\Addon\NotificationsDDoSAttacks\Parser\WanguardParser;
use WHMCS\Service\Service;

class ParsingNotifications implements TaskInterfaces
{
    private $frequency = '* * * * *';

    public $name = 'parsing notifications about DDoS attacks';

    function __construct()
    {
    }

    function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getFrequency(): string
    {
        return $this->frequency;
    }

    /**
     * @throws Exception
     * @throws \Throwable
     */
    function run(): void
    {
        ini_set('memory_limit', '2048M');

        $settings = SettingsModel::all()->keyBy('key')->transform(function ($val) {
            return $val->val;
        })->toArray();
        $userNotify = UserNotifyModel::all()->keyBy('user_id');

        $tasks = $this->getFormattedTaskList();
        $allowIpRanges = explode(PHP_EOL, $settings['allow_save_attack']);
        foreach ($tasks as $emailId => $task) {
            try {
                Capsule::transaction(function () use ($task, $emailId, $allowIpRanges, $settings, $userNotify) {
                    foreach ($task->getAttacks() as $attack) {
                        $save = false;

                        foreach ($allowIpRanges as $allowIpRange) {
                            if ($this->check_allow_ip($attack['ip'], $allowIpRange)) {
                                $save = true;
                            }
                        }

                        if (!$save) continue;

                        $notify = new NotifyModel();
                        $notify->service_id = $attack['service_id'];
                        $notify->ip = $attack['ip'];
                        $notify->proto = $attack['proto'];
                        $notify->bits = $attack['bits'];
                        $notify->packets = $attack['packets'];
                        $notify->start = $attack['start'];
                        $notify->end = $attack['end'];
                        if ($attack['service_id'] == 0) {
                            $notify->deleted_at = Carbon::now();
                        }
                        $notify->saveOrFail();

                        if (array_key_exists('clientNotifyEmailTemplate', $settings)) {
                            if ($attack['service_id'] == 0) continue;
                            $service = Service::find($attack['service_id']);
                            $user = Capsule::table("tblclients")->where("id", "=", $service->userid)->first();
                            $emailTemplate = Template::find($settings['clientNotifyEmailTemplate']);
                            if (empty($emailTemplate)) {
                                echo 'Отсутствут email шаблон,письмо не будет отправлено';
                            }
                            if (!empty($emailTemplate) && sendMessage($emailTemplate, 1, [
                                    'ip' => $attack['ip'],
                                    'proto' => $attack['proto'],
                                    'bits' => $attack['bits'],
                                    'packets' => $attack['packets'],
                                    'start' => $attack['start'],
                                    'end' => $attack['end'],
                                    'domain' => $service->domain,
                                    'product_name' => $service->product()->first()->name,
                                    'to' => [
                                        $user->email
                                    ]
                                ])) {
                                echo sprintf('Завершено для service_id: %s на почту: %s', $attack['service_id'], $user->email) . PHP_EOL;
                            } else {
                                echo sprintf('Письмо не отправлено для: service_id: %s на почту: %s', $attack['service_id'], $user->email) . PHP_EOL;
                            }

                        }
                    }

                    $emailParsed = new EmailParseModel();
                    $emailParsed->email_id = $emailId;
                    $emailParsed->saveOrFail();
                    echo 'email id-> ' . $emailId . ' parsed && saved' . PHP_EOL;
                }, 5);
            } catch (QueryException $e) {
                echo 'error email id->' . $emailId . PHP_EOL;
                echo 'email data ->' . json_encode($task->getRawEmail()) . PHP_EOL;
                echo $e->getMessage() . PHP_EOL;
                echo $e->getTraceAsString() . PHP_EOL;
            }
        }

        $this->rm(ROOTDIR . '/modules/addons/NotificationsDDoSAttacks/attachment_temp/');
        mkdir(ROOTDIR . '/modules/addons/NotificationsDDoSAttacks/attachment_temp/');
    }

    /**
     * @return NotifyDDoSAttackInterface[]
     * @throws Exception
     */
    private function getFormattedTaskList(): array
    {
        $result = [];
        $settings = SettingsModel::all()->keyBy('key')->transform(function ($val) {
            return $val->val;
        })->toArray();

        $mailBox = new Mailbox(sprintf("{%s:%s/imap/ssl/novalidate-cert}INBOX", $settings['imap_server_ip'], $settings['imap_server_port']), $settings['imap_server_login'], htmlspecialchars_decode($settings['imap_server_password']), ROOTDIR . '/modules/addons/NotificationsDDoSAttacks/attachment_temp/');
        $criteria = 'SINCE ' . date("j-M-Y", strToTime("-999 days"));
        $mailIds = collect($mailBox->searchMailbox($criteria))->splice(0, 100);
        $noParse = EmailParseModel::all()->pluck('email_id');
        foreach ($mailIds as $mailId) {
            if ($noParse->contains($mailId)) {
                continue;
            }

            $mail = $mailBox->getMail($mailId, false);

            switch (true) {
                case $mail->subject === 'DDoS Attack':
                    $result[$mailId] = new DDosGuardParser($mail);
                    echo 'ddg parse' . PHP_EOL;
                    break;
                case preg_match('/^\[Anomaly #\d+].+stopped.+$/', $mail->subject):
                    $result[$mailId] = new WanguardParser($mail);
                    echo 'wanguard parse' . PHP_EOL;
                    break;
                default:
                    continue 2;
            }
        }

        return $result;
    }


    function check_allow_ip($ip, $range)
    {
        if (strpos($range, '/') !== false) {
            // $range is in IP/NETMASK format
            list($range, $netmask) = explode('/', $range, 2);
            if (strpos($netmask, '.') !== false) {
                // $netmask is a 255.255.0.0 format
                $netmask = str_replace('*', '0', $netmask);
                $netmask_dec = ip2long($netmask);
                return ((ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec));
            } else {
                // $netmask is a CIDR size block
                // fix the range argument
                $x = explode('.', $range);
                while (count($x) < 4) $x[] = '0';
                list($a, $b, $c, $d) = $x;
                $range = sprintf("%u.%u.%u.%u", empty($a) ? '0' : $a, empty($b) ? '0' : $b, empty($c) ? '0' : $c, empty($d) ? '0' : $d);
                $range_dec = ip2long($range);
                $ip_dec = ip2long($ip);

                # Strategy 1 - Create the netmask with 'netmask' 1s and then fill it to 32 with 0s
                #$netmask_dec = bindec(str_pad('', $netmask, '1') . str_pad('', 32-$netmask, '0'));

                # Strategy 2 - Use math to create it
                $wildcard_dec = pow(2, (32 - $netmask)) - 1;
                $netmask_dec = ~$wildcard_dec;

                return (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
            }
        } else {
            // range might be 255.255.*.* or 1.2.3.0-1.2.3.255
            if (strpos($range, '*') !== false) { // a.b.*.* format
                // Just convert to A-B format by setting * to 0 for A and 255 for B
                $lower = str_replace('*', '0', $range);
                $upper = str_replace('*', '255', $range);
                $range = "$lower-$upper";
            }

            if (strpos($range, '-') !== false) { // A-B format
                list($lower, $upper) = explode('-', $range, 2);
                $lower_dec = (float)sprintf("%u", ip2long($lower));
                $upper_dec = (float)sprintf("%u", ip2long($upper));
                $ip_dec = (float)sprintf("%u", ip2long($ip));
                return (($ip_dec >= $lower_dec) && ($ip_dec <= $upper_dec));
            }
            return false;
        }
    }

    function rm($dir)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $path) {
            if ($path->isDir()) {
                rmdir((string)$path);
            } else {
                unlink((string)$path);
            }
        }
        rmdir($dir);
    }

}