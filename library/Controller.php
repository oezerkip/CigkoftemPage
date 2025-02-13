<?php

namespace library;

class Controller
{
    protected array $request = [];
    protected View $view;
    protected \PDO $db;
    protected Model $model;
    protected Validator $validator;

    public function __construct(array $request, View $view, \PDO $db)
    {
        $this->request = $request;
        $this->view = $view;
        $this->db = $db;
        $this->model = new Model($db);
        $this->validator = new Validator();
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Mainpage
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function index() : void {
        if (isset($_POST['registrationBtn'])) {
            $formConfig = [
                "vorname" => ["notempty"],
                "nachname" => ["notempty"],
                "email" => ["checkmail", "notempty"],
                "strasse" => ["notempty"],
                "plz" => ["isint", "zipcheck"],
                "wohnort" => ["notempty"],
                "passwort" => ["checkpwd", "notempty"],
                "passwort_wiederholen" => ["password_repetition"],
                "datenschutz" => ["isset"]
            ];
            $this->validator->setConfiguration($formConfig);
            $validationResult = $this->validator->validate($_POST);
            if($validationResult === true) {
                if ($_REQUEST['password'] == $_REQUEST['passwordRepeat']){
                    $uniqueMail = $this->model->uniqueMailCheck($_POST);
                    if($uniqueMail) {
                        $this->view->render('index', [
                            'uniqueEmail' => true
                        ]);
                    } else {
                        $this->model->registrationInsert($_POST);
                        $this->view->render('index', []);
                    }
                }
            } else {
                $this->view->render('index', [
                    'validationResult' => $validationResult
                ]);
            }
        }
        $this->view->render('index', []);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Error-Page
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function error() : void {
        $this->view->render('error', [
            'title' => 'Ein Fehler ist aufgetreten'
        ]);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Admin-Page
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function admin(): void {
        $this->view->render('admin', []);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Interne "ausgelagerte" Funktionen
    //////////////////////////////////////////////////////////////////////////////////////////////

    private function checkRegistration(): void {
        $formConfig = [
            "vorname" => ["notempty"],
            "nachname" => ["notempty"],
            "email" => ["checkmail", "notempty"],
            "strasse" => ["notempty"],
            "plz" => ["isint", "zipcheck"],
            "wohnort" => ["notempty"],
            "passwort" => ["checkpwd", "notempty"],
            "passwort_wiederholen" => ["password_repetition"],
            "datenschutz" => ["isset"]
        ];
        $this->validator->setConfiguration($formConfig);
        $validationResult = $this->validator->validate($_POST);
        if($validationResult === true) {
            if ($_REQUEST['password'] == $_REQUEST['passwordRepeat']){
                $uniqueMail = $this->model->uniqueMailCheck($_POST);
                if($uniqueMail) {
                    $this->view->render('index', [
                        'uniqueEmail' => true
                    ]);
                } else {
                    $this->model->registrationInsert($_POST);
                    $this->view->render('index', []);
                }
            }
        } else {
            $this->view->render('index', [
                'validationResult' => $validationResult
            ]);
        }
    }




}