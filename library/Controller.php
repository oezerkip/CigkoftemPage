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
            $this->checkRegistration($formConfig);
            return;
        }

        if (isset($_POST['loginBtn'])) {
            $formConfig = [
                "loginEmail" => ["notempty"],
                "loginPasswort" => ["notempty"]
            ];
            $this->checkUserLogin($formConfig);
            return;
        }

        if (isset($_POST['logoutBtn'])) {
            session_destroy();
            $this->view->render("index", []);
            return;
        }

        $this->view->render('index', []);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Admin-Page
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function admin(): void {
        if (isset($_POST['adminLoginBtn'])) {
            $formConfig = [
                'email' => ['notempty'],
                'passwort' => ['notempty']
            ];
            $this->checkAdminLogin($formConfig);
            return;
        }

        $this->view->render('admin', []);
    }

    public function impressum() {
        $this->view->render('impressum', []);
    }

    public function datenschutz() {
        $this->view->render('datenschutz', []);
    }




    /**** interne "ausgelagerte" Methoden *******/

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Interne "ausgelagerte" Funktionen
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function checkRegistration($formConfig): void {
        $this->validator->setConfiguration($formConfig);
        $validationResult = $this->validator->validate($_POST);
        if ($validationResult === true) {
            if ($_POST['passwort'] == $_POST['passwort_wiederholen']) {
                $uniqueMail = $this->model->uniqueMailCheck($_POST);
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

    public function checkAdminLogin($formConfig): void {
        $this->validator->setConfiguration($formConfig);
        $validationResult = $this->validator->validate($_POST);
        if ($validationResult === true) {
            $adminId = $this->model->checkAdminLogin($_POST);
            if ($adminId) {
                $_SESSION['admin'] = $adminId;
                if (isset($_SESSION['admin'])) {
                    $this->view->render("admin", []);
                }
            } else {
                $this->view->render("admin", [
                    'validationResult' => 'E-Mail oder Passwort ist falsch!'
                ]);
            }
        } else {
            $this->view->render("admin", [
                'validationResult' => $validationResult
            ]);
        }
    }

    public function checkUserLogin($formConfig): void {
        $this->validator->setConfiguration($formConfig);
        $validationResult = $this->validator->validate($_POST);
        if ($validationResult === true) {
            $userId = $this->model->checkUserLogin($_POST);
            var_dump($userId);
            if ($userId) {
                $_SESSION['user'] = $userId;
                var_dump($_SESSION);
                if ($_SESSION['user']) {
                    $this->view->render("index", []);
                }
            } else {
                $this->view->render("index", [
                    'validationResult' => 'E-Mail oder Passwort ist falsch!'
                ]);
            }
        } else {
            $this->view->render('index', [
                'validationResult' => $validationResult
            ]);
        }
    }

    public function logout(): void {
        session_destroy();
        $this->view->render("index", []);
    }
}