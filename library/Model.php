<?php

namespace library;

use PDO;

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
        $mail = $this->db->prepare("SELECT e_mail FROM customer WHERE e_mail=?");
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

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Login-Abfrage im User-Bereich
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function checkUserLogin($toBeChecked): array {
        $stmt = $this->db->prepare("SELECT ID, password FROM customer WHERE e_mail=?");
        $stmt->execute([$toBeChecked['loginEmail']]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Prüfen, ob der Benutzer existiert und das Passwort korrekt ist
        if ($user && password_verify($toBeChecked['loginPasswort'], $user['password'])) {
            return ['ID' => $user['ID']]; // Benutzer-ID zurückgeben
        } else {
            return ['ID' => 0];
        }
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Login-Abfrage im Admin-Bereich
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function checkAdminLogin($toBeChecked) {
        $adminId = $this->db->prepare("SELECT ID FROM admin WHERE e_mail=? AND password=?");
        $adminId->execute([$toBeChecked['email'], $toBeChecked['passwort']]);
        return $adminId->fetch(\PDO::FETCH_ASSOC);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Alle Gerichte aus der Datenbank holen
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function getFoodData () {
        $dataFood = $this->db->prepare("SELECT * FROM product");
        $dataFood->execute();
        return $dataFood->fetchAll(\PDO::FETCH_ASSOC); 
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Contest-Bilder Upload
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function uploadImages($userId, $imageURL) {
        $userId = 1;
        $stmt = $this->db->prepare("SELECT e_mail FROM customer WHERE ID = ?");
        $stmt->execute([$userId]);
        $userEmail = $stmt->fetchColumn();

        $stmt = $this->db->prepare("INSERT INTO contest (pic_url, owner_mail, rating, permission) VALUES (?,?,?,?)");
        $stmt->execute([$imageURL, $userEmail, '0', '0']);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Like Button
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function raiseRating($imageURL) {
        $stmt = $this->db->prepare("UPDATE contest SET rating=(SELECT rating FROM contest WHERE pic_url=$imageURL) + 1");
        $stmt->execute();
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Alle berechtigten Contest Bilder aus der Datenbank holen
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function getContestImages () {
        $contestImages = $this->db->prepare("SELECT ID, pic_url FROM contest WHERE permission = 1");
        $contestImages->execute();
        return $contestImages->fetchAll(\PDO::FETCH_ASSOC);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Siegerbild aus der Datenbank holen
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function getContestWinner () {
        $contestWinner = $this->db->prepare("SELECT pic_url FROM contest WHERE rating = (SELECT MAX(rating) FROM contest) LIMIT 1");
        $contestWinner->execute();
        return $contestWinner->fetch(\PDO::FETCH_ASSOC);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Eintragung von neuen Gerichten
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function addNewFood($foodToBeInserted) {
        $stmt = $this->db->prepare("INSERT INTO product (ean, name, description, additives, calories, price, special, category, image) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->execute(
            [$foodToBeInserted['ean'],
                $foodToBeInserted['name'],
                $foodToBeInserted['description'],
                $foodToBeInserted['additives'],
                $foodToBeInserted['calories'],
                $foodToBeInserted['price'],
                $foodToBeInserted['special'],
                $foodToBeInserted['category'],
                $foodToBeInserted['image']]);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Aktualisierung von Gerichten
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function updateFood($newFoodData) {
        $stmt = $this->db->prepare("UPDATE product SET ean=?, name=?, description=?, additives=?, calories=?, price=?, special=?, category=?, image=? WHERE ID=?");
        $stmt->execute([
            $newFoodData['ean'],
            $newFoodData['name'],
            $newFoodData['description'],
            $newFoodData['additives'],
            $newFoodData['calories'],
            $newFoodData['price'],
            $newFoodData['special'],
            $newFoodData['category'],
            $newFoodData['image'],
            $newFoodData['productID']
        ]);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Löschung von Gerichten
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function deleteFood($ean) {
        $stmt = $this->db->prepare("DELETE FROM product WHERE ean=?");
        $stmt->execute([$ean]);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Kunden aus der Datenbank suchen
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function getCustomer($mail): array {
        $stmt = $this->db->prepare("SELECT name, surname, e_mail, street, postal_code, city FROM customer WHERE e_mail=?");
        $stmt->execute([$mail]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Kunden aus der Datenbank löschen
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function deleteCustomer($mail) {
        $stmt = $this->db->prepare("DELETE FROM customer WHERE e_mail=?");
        $stmt->execute([$mail]);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Admin-Passwort Update
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function updateAdminPassword($pwd) {
        $stmt = $this->db->prepare("UPDATE admin SET password=? WHERE ID=1");
        $stmt->execute([$pwd['newPassword']]);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Admin-Passwort abfragen
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function checkAdminPassword($pwd) {
        $stmt = $this->db->prepare("SELECT password FROM admin WHERE password = ?");
        $stmt->execute([$pwd]);
        return $stmt->fetch();
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Contest-Bilder holen für die Admin-Seite
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function getContestForAdmin(): array {
        $stmt = $this->db->prepare("SELECT ID, pic_url, owner_mail, rating, permission FROM contest");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Bilderfreigabe
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function setPermission() {
        $stmt = $this->db->prepare("UPDATE contest SET permission=1");
        $stmt->execute();
    }

    //////////////////////////////////////////////////////////////////////////////////////////////
    /// Contest-Bilder löschen
    //////////////////////////////////////////////////////////////////////////////////////////////

    public function deleteContestImages($imageURL) {
        $stmt = $this->db->prepare("DELETE FROM contest WHERE oic_url=?");
        $stmt->execute([$imageURL]);
    }
}