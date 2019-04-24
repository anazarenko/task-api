<?php

use App\API;
use App\Utils\Response;

spl_autoload_register(function($className) {
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    include_once $_SERVER['DOCUMENT_ROOT'].'/'.$className.'.php';
});

try {
    $API = new API($_REQUEST['route'] ?? null);
    $API->run();
} catch (Exception $e) {
    Response::response(['status' => $e->getCode(), 'errorMessage' => $e->getMessage()], $e->getCode());
}