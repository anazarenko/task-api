<?php

namespace App\Module;

/**
 * Interface ModuleInterface
 * @package App\Module
 */
interface ModuleInterface
{
    /**
     * @return mixed
     */
    public function handle();
}