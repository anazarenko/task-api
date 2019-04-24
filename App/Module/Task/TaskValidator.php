<?php

namespace App\Module\Task;

use App\Utils\Response;

/**
 * Class TaskValidator
 * @package App\Module\Task
 */
class TaskValidator
{
    private $errors = [];

    /**
     * @param $inputData
     */
    public function validateCreation($inputData): void
    {
        $this->validateTitle($inputData);
        $this->validatePriority($inputData);
        $this->validateDueDate($inputData);

        if (\count($this->errors) > 0) {
            throw new \RuntimeException(implode('; ', $this->errors), Response::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @param $inputData
     */
    private function validateTitle($inputData): void
    {
        if (!filter_var($inputData->title ?? '', FILTER_DEFAULT)) {
            $this->errors['title'] = 'Invalid \'title\' value; ';
        }
    }

    /**
     * @param $inputData
     */
    private function validatePriority($inputData): void
    {
        if (!filter_var($inputData->title ?? '', FILTER_DEFAULT)) {
            $this->errors['title'] = 'Invalid \'title\' value';
        }

        if (!filter_var($inputData->priority ?? '', FILTER_CALLBACK, ['options' => function($value) {
            return \in_array($value, Task::PRIORITY, true);
        }])) {
            $this->errors['priority'] = '\'Invalid \'priority\' value';
        }
    }

    /**
     * @param $inputData
     */
    private function validateDueDate($inputData): void
    {
        try {
            $inputData->due_date = new \DateTime($inputData->due_date ?? '');

            if ($inputData->due_date->getTimestamp() <= (new \DateTime('now'))->getTimestamp()) {
                $this->errors['due_date'] = 'Invalid \'due_date\' format. Date must be in the future';
            }
        } catch (\Exception $e) {
            $this->errors['due_date'] = 'Invalid \'due_date\' format';
        }
    }
}