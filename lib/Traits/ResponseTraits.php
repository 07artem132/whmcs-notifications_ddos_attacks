<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 04.12.2019, 14:39
 *
 */

/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 04.12.2019, 14:14
 *
 */

namespace WHMCS\Module\Addon\NotificationsDDoSAttacks\Traits;

trait ResponseTraits
{

    /**
     * Send client message and die
     * @param string $status
     * @param string|null $message
     */
    function response(string $status, string $message = null, ?int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => $status,
            'message' => $message,
        ]);
        die();
    }

    /**
     * Send client data and die
     * @param int $status
     * @param string $data
     */
    function responseRawData(string $data, int $status = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo $data;
        die();
    }

    /**
     * Send client data and die
     * @param string $status
     * @param array $data
     */
    function responseData(string $status, array $data = []): void
    {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => $status,
            'data' => $data,
        ]);
        die();
    }

    /**
     * Send client errors array
     * @param string $status
     * @param array $data
     */
    function responseErrors(string $status, array $data = []): void
    {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode([
            'status' => $status,
            'errors' => $data,
        ]);
        die();
    }
}