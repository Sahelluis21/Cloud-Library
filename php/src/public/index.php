<?php
// public/index.php
session_start();

// Autoload (ou require de cada controller)
require __DIR__ . '/../controller/homecontroller.php';

// Redireciona para login se não estiver logado
//if (!isset($_SESSION['user_id'])) {
//    header("Location: ../view/login.php");
//    exit;
//}

// Cria o controlador e chama a ação padrão
$controller = new HomeController();
$controller->index();
