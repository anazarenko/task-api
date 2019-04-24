<?php

namespace App\Utils;

/**
 * Class PasswordEncoder
 * @package App\Utils
 */
class PasswordEncoder
{
    /**
     * @param string $password
     * @return bool|string
     */
    public static function encode(string $password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}