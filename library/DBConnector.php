<?php

namespace library;

class DBConnector
{
    private static \PDO|null $instance = null;
    public static array $config = [];

    private function __construct(){}
    private function __clone(){}

    public static function getInstance(): \PDO{
        if (!self::$instance){
            self::$instance = new \PDO(
                sprintf('mysql:host=%s;dbname=%s',self::$config['host'],self::$config['dbname']),
                self::$config['user'],
                self::$config['password']);
        }
        return  self::$instance;
    }
}