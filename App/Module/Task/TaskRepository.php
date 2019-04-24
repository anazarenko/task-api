<?php

namespace App\Module\Task;

use App\Module\AbstractRepository;
use App\Utils\Paginator;
use App\Utils\Request;
use App\Utils\Response;
use PDO;

/**
 * Class TaskRepository
 * @package App\Module\TaskRepository
 */
class TaskRepository extends AbstractRepository
{
    /**
     * @return string
     */
    protected function getDbTableName(): string
    {
        return 'task';
    }

    /**
     * @param int $userId
     * @return array
     */
    public function findByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare('SELECT id,title,priority,due_date,is_done FROM task WHERE user_id = :userId');
        $stmt->bindParam('userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $userId
     * @param Request $request
     * @return array
     */
    public function findByUserWithPagination(int $userId, Request $request): array
    {
        $requestParameters = $request->getArguments();
        $sort = 'id';
        $order = 'desc';

        if (isset($requestParameters['sort'])) {
            $sort = \in_array(strtolower($requestParameters['sort']), Task::SORTABLE_FIELDS, true) ? $requestParameters['sort'] : Task::SORTABLE_FIELDS[0];
        }

        if (isset($requestParameters['order'])) {
            $order = strtolower($requestParameters['order']) === 'desc' ? 'desc' : 'asc';
        }

        $queryString = "SELECT id,title,priority,due_date,is_done FROM task WHERE user_id = :userId ORDER BY {$sort} {$order}";
        $queryParameters = ['userId' => $userId];

        return (new Paginator($this->pdo))->paginate($queryString, $queryParameters, $requestParameters);
    }

    /**
     * @param int $taskId
     * @param int $userId
     * @return Task|null
     */
    public function findOneByIdAndUser(int $taskId, int $userId): ?Task
    {
        $task = null;
        $stmt = $this->pdo->prepare('SELECT * FROM task WHERE id = :taskId AND user_id = :userId LIMIT 1');
        $stmt->bindParam('taskId', $taskId, PDO::PARAM_INT);
        $stmt->bindParam('userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $task = new Task();
            $task->setId($result['id'])
                ->setTitle($result['title'])
                ->setPriority($result['priority'])
                ->setDueDate(new \DateTime($result['due_date']))
                ->setIsDone($result['is_done']);
        }

        return $task;
    }

    /**
     * @param Task $task
     * @param int $userId
     * @return Task
     * @throws \Exception
     */
    public function saveTask(Task $task, int $userId): Task
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO {$this->getDbTableName()} (title, is_done, user_id, priority, due_date)
                                               VALUES (:title, :is_done, :user_id, :priority, :dueDate)");

            $stmt->execute([
                'title' => $task->getTitle(),
                'is_done' => $task->getIsDone(),
                'user_id' => $userId,
                'priority' => $task->getPriority(),
                'dueDate' => $task->getDueDate()->format('Y-m-d H:i:s')
            ]);

            if ($stmt->errorCode() !== '00000') {
                throw new \RuntimeException($stmt->errorInfo()[2]);
            }

        } catch (\PDOException $e) {
            throw new \RuntimeException('Database saving error. '.$e->getMessage(), Response::STATUS_BAD_REQUEST);
        }

        $task->setId((int)$this->pdo->lastInsertId());

        return $task;
    }

    /**
     * @param int $taskId
     * @return bool
     */
    public function doneTask(int $taskId): bool
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE {$this->getDbTableName()} SET is_done = 1 WHERE id = :taskId");
            $stmt->bindParam('taskId', $taskId, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->errorCode() !== '00000') {
                throw new \RuntimeException($stmt->errorInfo()[2]);
            }

        } catch (\PDOException $e) {
            throw new \RuntimeException('Database saving error', Response::STATUS_BAD_REQUEST);
        }

        return true;
    }
}