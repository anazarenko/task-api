<?php

namespace App\Utils;

/**
 * Class Response
 * @package App\Utils
 */
class Response
{
    public const STATUS_OK = 200;
    public const STATUS_CREATED = 201;
    public const STATUS_BAD_REQUEST = 400;
    public const STATUS_UNAUTHORIZED = 401;
    public const STATUS_FORBIDDEN = 403;
    public const STATUS_NOT_FOUND = 404;

    /**
     * @param array $data
     * @param int $statusCode
     */
    public static function response(array $data, int $statusCode = self::STATUS_OK): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');
        header('Content-Type:application/json');
        http_response_code($statusCode);
        echo json_encode($data);
    }
}