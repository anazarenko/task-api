<?php

namespace App\Module\Task;

use App\Database\Database;
use App\Module\AbstractModule;
use App\Module\User\UserRepository;
use App\Utils\Auth;
use App\Utils\Response;

/**
 * Class TaskRepository
 * @package App\Module\TaskRepository
 */
class TaskModule extends AbstractModule
{
    use Auth;

    /** @var TaskRepository */
    private $taskRepository;
    /** @var UserRepository  */
    private $userRepository;

    /**
     * TaskModule constructor.
     * @param array $routeArguments
     */
    public function __construct(array $routeArguments)
    {
        parent::__construct($routeArguments);
        $dbConnection = (new Database())->getConnection();
        $this->taskRepository = new TaskRepository($dbConnection);
        $this->userRepository = new UserRepository($dbConnection);
    }

    /**
     * @return null|string
     */
    protected function getAction(): ?string
    {
        switch ($this->method) {
            case 'GET':
                return isset($this->routeArguments[1]) ? 'showTask' : 'listTask';
            case 'POST':
                return isset($this->routeArguments[1]) ? 'doneTask' : 'createTask';
            case 'DELETE':
                return isset($this->routeArguments[1]) ? 'deleteTask' : null;
            default:
                return null;
        }
    }

    /**
     * Get list action
     */
    public function listTask(): void
    {
        $user = $this->auth();

        Response::response($this->taskRepository->findByUserWithPagination($user->getId(), $this->request));
    }

    /**
     * Show task action
     */
    public function showTask(): void
    {
        $user = $this->auth();

        $taskId = (int)$this->routeArguments[1];
        $task = $this->taskRepository->findOneByIdAndUser($taskId, $user->getId());

        if ($task === null) {
            throw new \RuntimeException('Task not found', Response::STATUS_NOT_FOUND);
        }

        Response::response([
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'due_date' => $task->getDueDate()->format('d-m-Y H:i:s'),
            'priority' => $task->getPriority(),
            'isDone' => $task->getIsDone()
        ]);
    }

    /**
     * Create task action
     * @throws \Exception
     */
    public function createTask(): void
    {
        $user = $this->auth();

        $data = $this->getDataFromInput();
        $validator = new TaskValidator();
        $validator->validateCreation($data);

        $task = new Task();
        $task->setTitle($data->title)
            ->setPriority($data->priority)
            ->setDueDate($data->due_date)
            ->setIsDone(0);

        $this->taskRepository->saveTask($task, $user->getId());

        Response::response([
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'due_date' => $task->getDueDate()->format('d-m-Y H:i:s'),
            'priority' => $task->getPriority(),
            'isDone' => $task->getIsDone()
        ], Response::STATUS_CREATED);
    }

    /**
     * Done task
     */
    public function doneTask(): void
    {
        $user = $this->auth();

        $taskId = (int)$this->routeArguments[1];
        $task = $this->taskRepository->findOneByIdAndUser($taskId, $user->getId());

        if ($task === null) {
            throw new \RuntimeException('Task not found', Response::STATUS_NOT_FOUND);
        }

        $this->taskRepository->doneTask($task->getId());

        Response::response([
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'due_date' => $task->getDueDate()->format('d-m-Y H:i:s'),
            'priority' => $task->getPriority(),
            'isDone' => 1
        ]);
    }

    /**
     * Delete list action
     */
    public function deleteTask(): void
    {
        $user = $this->auth();

        $taskId = (int)$this->routeArguments[1];
        $task = $this->taskRepository->findOneByIdAndUser($taskId, $user->getId());

        if ($task === null) {
            throw new \RuntimeException('Task not found', Response::STATUS_NOT_FOUND);
        }

        $this->taskRepository->deleteOneById($taskId);

        Response::response([]);
    }
}