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
        header("Location: /auth/login.php");
        exit;
    }
    require_once __DIR__ . '/../controller/homecontroller.php';
    $controller = new HomeController();
    $controller->index();
}
if ($uri === '/upload' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__ . '/../class/controlador.php';
    $controlador = new Controlador($conn);
    $userId = $_SESSION['user_id'] ?? null;
    $result = $controlador->upload($_FILES, $userId);

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
