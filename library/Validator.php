<?php

namespace library;

class Validator
{
    public $validationConfig = [];

    public function __construct(){
    }

    public function setConfiguration($config): void {
        $this->validationConfig = $config;
    }


    public function validate($values){
        $result = array();
        foreach ($values as $field => $value){
            if (isset($this->validationConfig[$field])){
                foreach ($this->validationConfig[$field] as $rule){
                    switch ($rule)
                    {
                        // Prüfung auf fehlende Eingabe
                        case "notempty":
                            if (strlen(trim($value)) == 0){
                                $result[$field] = "Feld darf nicht leer sein! ";
                            }
                            break;
                        // Prüfung dass nur Zahlen im Feld stehen dürfen
                        case "isint":
                            if (!is_numeric(trim($value))) {
                                $result[$field] = "Es dürfen nur Zahlen im Feld stehen!";
                            }
                            break;
                        // Prüft Eingabe auf die Gültigkeit der E-Mail
                        case "checkmail":
                            if (!filter_var(trim($value), FILTER_VALIDATE_EMAIL)) {
                                $result[$field] = "Ungültige E-Mail!";
                            }
                            break;
                        case "checkpwd":
                            // Prüft ein Feld, dass mindestens 8
                            if (strlen(trim($value)) < 8) {
                                $result[$field] = "Das Passwort muss mindestens 8 Zeichen enthalten!";
                            }
                            // Prüft ein Feld, dass mindestens eine Zahl vorhanden ist
                            if (!preg_match("#[0-9]+#", $value)) {
                                $result[$field] = "Passwort muss eine Zahl enthalten!";
                            }
                            // Prüft ein Feld, dass mindestens ein Kleinbuchstabe vorhanden ist
                            if (!preg_match("#[a-z]+#", $value)) {
                                $result[$field] = "Passwort muss Kleinbuchstaben enthalten!";
                            }
                            // Prüft ein Feld, dass mindestens ein Großbuchstabe vorhanden ist
                            if (!preg_match("#[A-Z]+#", $value)) {
                                $result[$field] = "Passwort muss Großbuchstaben enthalten!";
                            }
                            break;
                        case "password_repetition":
                            if ($_POST['passwort_wiederholen'] != $_POST['passwort']) {
                                $result[$field] = "Passwörter stimmen nicht überein!";
                            }
                            break;
                        case "zipcheck":
                            // Prüft ein Feld, dass mindestens fünf und höchstens 5 Zeichen vorhanden sind
                            if (strlen($value) == 5) {
                                $result[$field] = "Postleitzahl ungültig!";
                            }
                            break;
                        case "isset":
                            if (!isset($_POST['datenschutz'])) {
                                $result[$field] = "Datenschutzregeln bestätigen";
                            }
                    }
                }
            }
        }
        if (count($result) == 0) {
            return true;
        }
        return $result;
    }
}