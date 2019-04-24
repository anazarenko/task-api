<?php

namespace App\Module\User;

use App\Module\AbstractRepository;
use App\Utils\Response;

/**
 * Class TokenRepository
 * @package App\Module\User
 */
class TokenRepository extends AbstractRepository
{
    /**
     * @return string
     */
    protected function getDbTableName(): string
    {
        return 'token';
    }

    /**
     * @param string $token
     * @param int $userId
     * @return bool
     */
    public function saveToken(string $token, int $userId): bool
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO {$this->getDbTableName()} (token, user_id)
                                                   VALUES (:token, :user_id)");
            $stmt->execute([
                'token' => $token,
                'user_id' => $userId
            ]);

        } catch (\PDOException $e) {
            throw new \RuntimeException('Database saving error', Response::STATUS_BAD_REQUEST);
        }

        return $token;
    }
}