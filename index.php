<?php
session_start();

use library\Application;

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    spl_autoload_register(function ($class) {
        $path = './' . lcfirst(str_replace('\\', '/', $class)) . '.php';
        require $path;
    });

    $app = new Application();
    $app->dispatch($_POST);
} catch (\Exception $e) {
    var_dump($e);
    exit(1);
}
exit;
