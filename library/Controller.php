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
            $this->checkRegistration();
            return;
        }

        if (isset($_POST['loginBtn'])) {
            $this->checkUserLogin();
            return;
        }

        if (isset($_POST['logoutBtn'])) {
            session_destroy();
            $_SESSION = [];
            $this->view->render("index", []);
            return;
        }

        $foodMenu = $this->model->getFoodData();
        $specialFood = $this->model->getSpecialFood();
        //$contestImages = $this->model->getContestImages();
        //$contestWinner = $this->model->getContestWinner();

        $this->view->render('index', [
            'foodMenu' => $foodMenu,
            'specialFood' => $specialFood
        ]);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Admin-Page
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function admin(): void {
        if (isset($_POST['adminLoginBtn'])) {
            $this->testLogin();
            return;
        }

        $this->view->render('admin', []);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Impressum-Page
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function impressum() {
        $this->view->render('impressum', []);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Datenschutz-Page
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function datenschutz() {
        $this->view->render('datenschutz', []);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// AJAX Content Loading
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function loadContent(): void {
        if (!isset($_GET['content'])) {
            echo "Kein Inhalt angegeben.";
            return;
        }
    
        $content = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['content']); // Sicherheit: Filtert ungültige Zeichen
        $filePath = "templates/content/{$content}.phtml";
    
        if (file_exists($filePath)) {
            ob_start();
            include $filePath;
            $output = ob_get_clean();
            echo $output;
        } else {
            echo "Datei nicht gefunden.";
        }
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Interne "ausgelagerte" Funktionen
    //////////////////////////////////////////////////////////////////////////////////////////////

    /*************** Validierung des Anmeldeformulars und DB Insert ***************/

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
            "datenschutz" => ["isCheck"]
        ];
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

    /**************************** Login Check Adminbereich ****************************/

    public function checkAdminLogin(): void {
        $formConfig = [
            'email' => ['notempty'],
            'passwort' => ['notempty']
        ];
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

    /***************************** Login Check Userbereich *****************************/

    public function checkUserLogin(): void {
        $formConfig = [
            "loginEmail" => ["notempty"],
            "loginPasswort" => ["notempty"]
        ];
        $this->validator->setConfiguration($formConfig);
        $validationResult = $this->validator->validate($_POST);
        if ($validationResult === true) {
            $userId = $this->model->checkUserLogin($_POST);
            if ($userId) {
                $_SESSION['user'] = $userId;
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

    public function testLogin() : void {
        $_SESSION ['admin'] = 1;

        $this->view->render("admin", []);
    }
}