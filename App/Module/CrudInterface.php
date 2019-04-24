<?php

namespace App\Module;

/**
 * Interface CrudInterface
 * @package App\Module
 */
interface CrudInterface
{
    /**
     * @return mixed
     */
    public function getAll(): array;
}