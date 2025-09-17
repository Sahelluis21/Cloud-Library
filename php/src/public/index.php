<?php
// public/index.php
session_start();

$uri = $_SERVER['REQUEST_URI'];

if ($uri === '/login') {
    require_once __DIR__ . '/../controller/authcontroller.php';
    $controller = new AuthController();
    $controller->login();
} elseif ($uri === '/logout') {
    require_once __DIR__ . '/../controller/authcontroller.php';
    $controller = new AuthController();
    $controller->logout();
} else {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /login");
        exit;
    }
    require_once __DIR__ . '/../controller/homecontroller.php';
    $controller = new HomeController();
    $controller->index();
}
