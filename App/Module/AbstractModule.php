<?php

namespace App\Module;

use App\Utils\Request;
use App\Utils\Response;

/**
 * Class AbstractModule
 * @package App\Entity
 */
abstract class AbstractModule implements ModuleInterface
{
    /** @var string */
    protected $method;
    /** @var array */
    protected $routeArguments;
    /** @var Request */
    protected $request;

    /**
     * AbstractModule constructor.
     * @param array $routeArguments
     */
    public function __construct(array $routeArguments)
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->routeArguments = $routeArguments;
        $this->request = new Request();
    }

    /**
     * @return mixed|void
     * @throws \Exception
     */
    public function handle()
    {
        $action = $this->getAction();

        if ($action === null || !method_exists($this, $action)) {
            throw new \RuntimeException('Route does not exist', Response::STATUS_NOT_FOUND);
        }

        $this->$action();
    }

    /**
     * @return object
     */
    protected function getDataFromInput(): object
    {
        $data = json_decode(file_get_contents('php://input'));

        if (!\is_object($data)) {
            throw new \RuntimeException('Invalid JSON input format', Response::STATUS_BAD_REQUEST);
        }

        return $data;
    }

    /**
     * @return null|string
     */
    abstract protected function getAction(): ?string;
}