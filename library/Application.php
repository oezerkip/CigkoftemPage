<?php

namespace library;

class Application
{
    const BASE_PATH = __DIR__.DIRECTORY_SEPARATOR.'../';
    const LIBRARY_PATH = __DIR__.DIRECTORY_SEPARATOR;
    const CONFIG_PATH = self::BASE_PATH.'config'.DIRECTORY_SEPARATOR;
    const TEMPLATE_BASE_PATH = self::BASE_PATH.'templates'.DIRECTORY_SEPARATOR;

    protected \PDO $db;
    protected $view = null;
    public array $config = [];

    public function __construct() {
        $this->bootstrap();
    }

    protected function bootstrap() : void
    {
        $this->config = require_once self::CONFIG_PATH.'dbconfig.php';
        $this->view = new View();
        DBConnector::$config = $this->config['db'];
        $this->db = DBConnector::getInstance();

    }

    public function dispatch(array $request) : void
    {
        $page = isset($request['page'])?strtolower($request['page']):'index';
        $controller = new Controller($request, $this->view, $this->db);
        if (method_exists($controller, $page)) {
            $controller->{$page}();
        } else {
            $controller->error();
        }
    }
}