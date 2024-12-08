<?php
require_once 'classes/db.php';

class User extends Database {
    public function login($username, $password) {
        $conn = $this->getConnection();
        $query = "SELECT * FROM users WHERE username = ? AND password = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function register($username, $email, $password) {
        $conn = $this->getConnection();
        $query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $username, $email, $password);
        return $stmt->execute();
    }
}
?>
