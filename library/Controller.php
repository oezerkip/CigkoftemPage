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
//        echo ("POST:\n");
//        echo '<pre>';
//        var_dump($_POST);
//        echo '</pre>';

        if (isset($_POST['registrationBtn'])) {
            $this->checkRegistration();
            return;
        }
        $this->view->render('index', []);
    }


    public function impressum() {
        $this->view->render('impressum', []);
    }

    public function datenschutz() {
        $this->view->render('datenschutz', []);
    }


    /**** interne "ausgelagerte" Methoden *******/

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Admin-Page
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function admin(): void {
        $this->view->render('admin', []);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Interne "ausgelagerte" Funktionen
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function checkRegistration(): void {
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
        if ($validationResult === true) {
            if ($_POST['passwort'] == $_POST['passwort_wiederholen']) {
                $uniqueMail = $this->model->uniqueMailCheck($_POST);
                var_dump("uniqueMail: " . $uniqueMail);
                if ($uniqueMail) {
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