<?php

namespace App\Module\Task;

/**
 * Class Task
 * @package App\Module\Task
 */
class Task
{
    public const LOW = 'LOW';
    public const NORMAL = 'NORMAL';
    public const HIGH = 'HIGH';

    public const PRIORITY = [self::LOW, self::NORMAL, self::HIGH];
    public const SORTABLE_FIELDS = ['id', 'priority', 'due_date'];

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var int
     */
    private $isDone;

    /**
     * @var \DateTime
     */
    private $dueDate;

    /**
     * @var string
     */
    private $priority = self::LOW;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Task
     */
    public function setId(int $id): Task
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Task
     */
    public function setTitle(string $title): Task
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return int
     */
    public function getIsDone(): int
    {
        return $this->isDone;
    }

    /**
     * @param int $isDone
     * @return Task
     */
    public function setIsDone(int $isDone): Task
    {
        $this->isDone = $isDone;
        return $this;
    }

    /**
     * @return string
     */
    public function getPriority(): string
    {
        return $this->priority;
    }

    /**
     * @param string $priority
     * @return Task
     */
    public function setPriority(string $priority): Task
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDueDate(): \DateTime
    {
        return $this->dueDate;
    }

    /**
     * @param \DateTime $dueDate
     * @return Task
     */
    public function setDueDate(\DateTime $dueDate): Task
    {
        $this->dueDate = $dueDate;
        return $this;
    }
}