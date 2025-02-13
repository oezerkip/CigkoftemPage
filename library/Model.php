<?php

namespace library;

class Model
{

    protected \PDO $db;

    function __construct($db) {
        $this->db = $db;
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Beziehen aller User aus der Datenbank
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function getData(): array {
        $data = $this->db->prepare("SELECT * FROM customer");
        $data->execute();
        return $data->fetchAll();
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Passwort Prüfung für den Login
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function uniqueMailCheck(array $mailCheck): bool|array {
        $mail = $this->db->prepare("SELECT email FROM customer WHERE e_mail=?");
        $mail->execute([$mailCheck['email']]);
        $uniqueMail = $mail->fetch(\PDO::FETCH_ASSOC);
        if($uniqueMail) {
            return true;
        }
        return false;
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// User-Registration und Insert in die DB
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function registrationInsert(array $toBeInserted) : void {
        $user = $this->db->prepare("INSERT INTO customer (name, surname,e_mail, password, street, postal_code, city) VALUES (?,?,?,?,?,?,?)");
        $user->execute(
            [$toBeInserted['vorname'],
                $toBeInserted['nachname'],
                $toBeInserted['email'],
                password_hash($toBeInserted['passwort'], PASSWORD_BCRYPT),
                $toBeInserted['strasse'],
                $toBeInserted['plz'],
                $toBeInserted['wohnort']
                ]);
    }
}