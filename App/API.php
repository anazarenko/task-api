<?php

namespace App;

use App\Module\ModuleInterface;
use App\Module\Task\TaskModule;
use App\Module\User\UserModule;

/**
 * Class TaskAPI
 * @package App
 */
class API
{
    /** @var array */
    protected $args = [];

    /** @var ModuleInterface[] */
    protected $modules = [];

    /**
     * TaskAPI constructor.
     * @param string $route
     * @throws \Exception
     */
    public function __construct(?string $route)
    {
        $this->args = explode('/', rtrim($route, '/'));

        if ($route === null || \count($this->args) === 0) {
            throw new \RuntimeException('Route does not exist', 404);
        }

        $this->modules = [
            'task' => TaskModule::class,
            'user' => UserModule::class
        ];
    }

    /**
     * Run API
     */
    public function run(): void
    {
        $moduleCLass = $this->getModule();

        if ($moduleCLass === null) {
            throw new \RuntimeException('Route does not exist', 404);
        }

        /** @var ModuleInterface $module */
        $module = new $moduleCLass($this->args);
        $module->handle();
    }

    /**
     * @return string|null
     */
    private function getModule(): ?string
    {
        foreach ($this->modules as $moduleName => $moduleClass) {
            if ($moduleName === $this->args[0]) {
                return $moduleClass;
            }
        }

        return null;
    }
}