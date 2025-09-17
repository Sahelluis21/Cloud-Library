<?php
// model/User.php
require_once 'db_connect.php';

class User {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }


     public function getUsername($id) {
        $stmt = $this->conn->prepare("SELECT username FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['username'] : null;
    }

    
    // Criar novo usuário
    public function createUser($username, $password) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("
            INSERT INTO users (username, password_hash) 
            VALUES (:username, :password_hash)
        ");
        return $stmt->execute([
            ':username' => $username,
            ':password_hash' => $passwordHash
        ]);
    }

    // Login: retorna usuário se válido, senão false
    public function login($username, $password) {
        $stmt = $this->conn->prepare("
            SELECT * FROM users WHERE username = :username
        ");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return false;
    }

    // Buscar usuário pelo ID
    public function getUserById($id) {
        $stmt = $this->conn->prepare("
            SELECT id, username FROM users WHERE id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Logout (remove sessão)
    public static function logout() {
        session_start();
        session_unset();
        session_destroy();
    }
}
