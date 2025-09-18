<?php
// controller/AuthController.php
require_once __DIR__ . '/../model/user.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $user = $this->userModel->login($username, $password);

            if ($user) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                header("Location: /"); // vai para Home
                exit;
            } else {
                $error = "Usuário ou senha inválidos!";
            }
        }
        require __DIR__ . '/../public/auth/login.php';
    }

    public function logout() {
        User::logout();
        header("Location: /auth/login.php");
        exit;
    }
}
