<?php

namespace library;

class Controller
{
    protected array $request = [];
    protected View $view;
    //protected \PDO $db;

    public function __construct(array $request, View $view,/* \PDO $db*/)
    {
        $this->request = $request;
        $this->view = $view;
        //$this->db = $db;
    }

    public function index() : void {
        $this->view->render('index', [
            'title' => 'Cigköftem'
        ]);
        
    }

    public function error() : void {
        $this->view->render('error', [
            'title' => 'Ein Fehler ist aufgetreten'
        ]);
    }
}