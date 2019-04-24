<?php

namespace App\Utils;

/**
 * Class Request
 * @package App\Utils
 */
class Request
{
    /** @var array */
    protected $arguments = [];

    /** @var int */
    protected $page = 1;

    /** @var int */
    protected $limit = 10;

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->arguments = $_REQUEST;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }
}