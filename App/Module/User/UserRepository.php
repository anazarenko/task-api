<?php

namespace App\Module\User;

use App\Module\AbstractRepository;
use App\Utils\Response;
use PDO;

/**
 * Class UserRepository
 * @package App\Module\User
 */
class UserRepository extends AbstractRepository
{
    /**
     * @return string
     */
    protected function getDbTableName(): string
    {
        return 'user';
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function findOneByEmail(string $email): ?User
    {
        $user = null;
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->getDbTableName()} WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $user = new User();
            $user->setId($result['id'])
                ->setEmail($result['email'])
                ->setPassword($result['password']);
        }

        return $user;
    }

    /**
     * @param string $token
     * @return User|null
     */
    public function findOneByToken(string $token): ?User
    {
        $user = null;
        $stmt = $this->pdo->prepare("SELECT u.* FROM user u JOIN token t on t.user_id = u.id WHERE t.token = :token LIMIT 1");
        $stmt->execute(['token' => $token]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $user = new User();
            $user->setId((int)$result['id'])
                ->setEmail($result['email'])
                ->setPassword($result['password']);
        }

        return $user;
    }

    /**
     * @param User $user
     * @return User
     */
    public function saveUser(User $user): User
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO {$this->getDbTableName()} (email, password)
                                               VALUES (:email, :password)");
            $stmt->execute([
                'email' => $user->getEmail(),
                'password' => $user->getPassword()
            ]);

            if ($stmt->errorCode() !== '00000') {
                throw new \RuntimeException($stmt->errorInfo()[2]);
            }

        } catch (\PDOException $e) {
            throw new \RuntimeException('Database saving error', Response::STATUS_BAD_REQUEST);
        }

        $user->setId((int)$this->pdo->lastInsertId());

        return $user;
    }
}