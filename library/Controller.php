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

    public function index(): void
    {
        $foodMenu = $this->model->getFoodData();
        $contestWinner = $this->model->getContestWinner();

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
            $this->view->render("index", [
                'foodMenu' => $foodMenu,
                'contestWinner' => $contestWinner
            ]);
            return;
        }

        

        $this->view->render('index', [
            'foodMenu' => $foodMenu,
            'contestWinner' => $contestWinner
        ]);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Admin-Page
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function admin(): void
    {
        if (isset($_POST['adminLoginBtn'])) {
            $this->testLogin();
            return;
        }


        $this->view->render('admin', []);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Impressum-Page
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function impressum()
    {
        $this->view->render('impressum', []);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Datenschutz-Page
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function datenschutz()
    {
        $this->view->render('datenschutz', []);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// AJAX Content Loading
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function loadContent(): void
    {
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

    public function checkRegistration(): void
    {
        $foodMenu = $this->model->getFoodData();
        $contestWinner = $this->model->getContestWinner();
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
                        'uniqueEmail' => true,
                        'foodMenu' => $foodMenu,
                        'contestWinner' => $contestWinner
                    ]);
                } else {
                    $this->model->registrationInsert($_POST);
                    $this->view->render('index', [
                        'foodMenu' => $foodMenu,
                        'contestWinner' => $contestWinner
                    ]);
                }
            }
        } else {
            $this->view->render('index', [
                'validationResult' => $validationResult,
                'foodMenu' => $foodMenu,
                'contestWinner' => $contestWinner
            ]);
        }
    }

    /**************************** Login Check Adminbereich ****************************/

    public function checkAdminLogin(): void
    {
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

    public function checkUserLogin(): void
    {
        $foodMenu = $this->model->getFoodData();
        $contestWinner = $this->model->getContestWinner();
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
                    $this->view->render("index", [
                        'foodMenu' => $foodMenu,
                        'contestWinner' => $contestWinner
                    ]);
                }
            } else {
                $this->view->render("index", [
                    'validationResult' => 'E-Mail oder Passwort ist falsch!',
                    'foodMenu' => $foodMenu,
                    'contestWinner' => $contestWinner
                ]);
            }
        } else {
            $this->view->render('index', [
                'validationResult' => $validationResult,
                'foodMenu' => $foodMenu,
                'contestWinner' => $contestWinner
            ]);
        }
    }

    public function testLogin(): void
    {
        $_SESSION['admin'] = 1;

        $this->view->render("admin", []);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Produkte aus der DB holen
    //////////////////////////////////////////////////////////////////////////////////////////////
    public function getFood(): void
    {
        header('Content-Type: application/json; charset=UTF-8'); // Sicherstellen, dass JSON richtig kodiert ist

        $foodMenu = $this->model->getFoodData();

        // Prüfen, ob die Daten leer sind
        if (empty($foodMenu)) {
            echo json_encode(["error" => "Keine Daten gefunden"]);
            return;
        }

        // Prüfen, ob json_encode fehlschlägt
        $json = json_encode($foodMenu, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);

        if ($json === false) {
            echo json_encode(["error" => "JSON-Fehler: " . json_last_error_msg()]);
            return;
        }

        // Ausgabe des JSON
        echo $json;
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Contest aus der DB holen für Frontend
    //////////////////////////////////////////////////////////////////////////////////////////////
    public function getContest(): void
    {
        header('Content-Type: application/json; charset=UTF-8'); // Sicherstellen, dass JSON richtig kodiert ist

        $contest = $this->model->getContestImages();

        // Prüfen, ob die Daten leer sind
        if (empty($contest)) {
            echo json_encode(["error" => "Keine Daten gefunden"]);
            return;
        }

        // Prüfen, ob json_encode fehlschlägt
        $json = json_encode($contest, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);

        if ($json === false) {
            echo json_encode(["error" => "JSON-Fehler: " . json_last_error_msg()]);
            return;
        }

        // Ausgabe des JSON
        echo $json;
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    ///  Contest aus der DB holen für Backend
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function getContestForAdmin(): void
    {
        header('Content-Type: application/json; charset=UTF-8'); // Sicherstellen, dass JSON richtig kodiert ist

        $contest = $this->model->getContestForAdmin();

        // Prüfen, ob die Daten leer sind
        if (empty($contest)) {
            echo json_encode(["error" => "Keine Daten gefunden"]);
            return;
        }

        // Prüfen, ob json_encode fehlschlägt
        $json = json_encode($contest, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);

        if ($json === false) {
            echo json_encode(["error" => "JSON-Fehler: " . json_last_error_msg()]);
            return;
        }

        // Ausgabe des JSON
        echo $json;
    }


    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Contest Image in Datenbank hochladen
    //////////////////////////////////////////////////////////////////////////////////////////////
    public function uploadContestImage(): void
    {
        $imagePath = htmlentities($_GET['imagePath']);
        $_SESSION['id'] = 1;
        if (isset($_SESSION['id'])) {
            echo '
            <div class="alert alert-success" role="alert">
                Vielen Dank für dein Dick-Pic ;)
            </div>
            ';
            $this->model->uploadImages($_SESSION['id'], $imagePath);
        } else {
            echo '
            <div class="alert alert-danger" role="alert">
                Penis zu klein!
            </div>
            ';
        }
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Gerichte in die Datenbank hochladen
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function addArticleToDB(): void
    {
        var_dump(test);
        $newFood['ean'] = htmlentities($_GET['ean']);
        $newFood['category'] = htmlentities($_GET['category']);
        $newFood['name'] = htmlentities($_GET['name']);
        $newFood['special'] = htmlentities($_GET['special']);
        $newFood['description'] = htmlentities($_GET['description']);
        $newFood['additives'] = htmlentities($_GET['additives']);
        $newFood['calories'] = htmlentities($_GET['calories']);
        $newFood['price'] = htmlentities($_GET['price']);
        $newFood['image'] = htmlentities($_GET['image']);
        $stmt = $this->model->addNewFood($newFood);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Gerichte in der Datenbank aktualisieren
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function updateArticleAtDB(): void
    {
        $food['productID'] = htmlentities($_GET['productID']);
        $food['ean'] = htmlentities($_GET['ean']);
        $food['category'] = htmlentities($_GET['category']);
        $food['name'] = htmlentities($_GET['name']);
        $food['special'] = htmlentities($_GET['special']);
        $food['description'] = htmlentities($_GET['description']);
        $food['additives'] = htmlentities($_GET['additives']);
        $food['calories'] = htmlentities($_GET['calories']);
        $food['price'] = htmlentities($_GET['price']);
        $food['image'] = htmlentities($_GET['image']);
        $stmt = $this->model->updateFood($food);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Gerichte aus der Datenbank löschen
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function deleteArticleFromDB(): void
    {
        $foodEan = htmlentities($_GET['ean']);
        $stmt = $this->model->deleteFood($foodEan);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Kunden aus der Datenbank suchen
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function getCustomerFromDB(): void {
        $customer = htmlentities($_GET['searchMail']);
        $stmt = $this->model->getCustomer($customer);

        // Wenn kein Ergebnis gefunden wurde, kannst du ein leeres Array zurückgeben
        if ($stmt) {
            echo json_encode($stmt); // Gebe die Daten als JSON zurück
        } else {
            echo json_encode([]); // Falls kein Kunde gefunden wurde
        }
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Kunden aus der Datenbank löschen
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function deleteCustomerFromDB() {
        $customerEmail = htmlentities($_GET['customerEmail']);
        $stmt = $this->model->deleteCustomer($customerEmail);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Admin-Passwort aktualsieren
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function updateAdminPassword(){
        $pwd['currentPassword'] = htmlentities($_GET['currentPassword']);
        $pwd['newPassword'] = htmlentities($_GET['newPassword']);
        $pwd['newPasswordRepeat'] = htmlentities($_GET['newPasswordRepeat']);

        $stmt = $this->model->checkAdminPassword($pwd['currentPassword']);
        if ($stmt['password'] !=  $pwd['currentPassword']) {
           echo '<div class="alert alert-danger" role="alert">Passwort nicht gefunden!</div>';
           return;
        } else {
           if($pwd['newPassword'] != $pwd['newPasswordRepeat']) {
                echo '<div class="alert alert-warning" role="alert">Neue Passwörter stimmen nicht überein!</div>';
                return;
           } else {
                $stmt = $this->model->updateAdminPassword($pwd);
                echo '<div class="alert alert-success" role="alert">Passwort wurde aktualisiert!</div>';
           }
        }
    }


}
