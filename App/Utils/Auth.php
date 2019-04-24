<?php
/**
 * Created by PhpStorm.
 * User: oleksandrnazarenko
 * Date: 4/23/19
 * Time: 5:49 PM
 */

namespace App\Utils;

use App\Module\User\User;
use App\Module\User\UserRepository;

/**
 * Trait Auth
 * @package App\Utils
 * @property UserRepository $userRepository
 */
trait Auth
{
    /**
     * @return User
     */
    protected function auth(): User
    {
        $token = $this->getBearerToken();

        if ($token === null) {
            throw new \RuntimeException('Invalid Bearer token', Response::STATUS_FORBIDDEN);
        }

        $user = $this->userRepository->findOneByToken($token);

        if ($user === null) {
            throw new \RuntimeException('User not found', Response::STATUS_UNAUTHORIZED);
        }

        return $user;
    }

    /**
     * @return null|string
     */
    protected function getBearerToken(): ?string
    {
        $headers = $this->getAuthorizationHeader();
        if ($headers !== null) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * @return null|string
     */
    private function getAuthorizationHeader(): ?string
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER['Authorization']);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
        } elseif (\function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }

        return $headers;
    }
}