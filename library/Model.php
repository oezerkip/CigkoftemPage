<?php

namespace library;

class Model
{
    protected \PDO $db;

    function __construct($db) {
        $this->db = $db;
    }

    function getData() {
        $data = $this->db->prepare("SELECT * FROM customer");
        $data->execute();
        return $data->fetchAll();
    }
}