<?php

namespace App\Module\User;

use App\Utils\Response;

/**
 * Class UserValidator
 * @package App\Module\Task
 */
class UserValidator
{
    private $errors = [];

    /**
     * @param $inputData
     */
    public function validateAuth($inputData): void
    {
        $this->validateEmail($inputData);
        $this->validatePassword($inputData);

        if (\count($this->errors) > 0) {
            throw new \RuntimeException('Invalid email or password', Response::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @param $inputData
     */
    private function validateEmail($inputData): void
    {
        if (!filter_var($inputData->email ?? '', FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'Invalid \'email\' value';
        }
    }

    /**
     * @param $inputData
     */
    private function validatePassword($inputData): void
    {
        if (!filter_var($inputData->password ?? '', FILTER_DEFAULT)) {
            $this->errors['title'] = 'Invalid \'password\' value';
        }
    }
}