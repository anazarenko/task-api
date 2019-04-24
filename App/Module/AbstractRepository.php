<?php

namespace App\Module;

use PDO;

/**
 * Class AbstractModule
 * @package App\Entity
 */
abstract class AbstractRepository implements CrudInterface
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * TaskRepository constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM {$this->getDbTableName()}");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function findOneById(int $id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->getDbTableName()} WHERE id = :id LIMIT 1");
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function deleteOneById(int $id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->getDbTableName()} WHERE id = :id LIMIT 1");
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @return string
     */
    abstract protected function getDbTableName(): string;
}