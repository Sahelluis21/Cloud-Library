<?php
require __DIR__ . '/../class/controlador.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controlador = new Controlador($conn);
    $controlador->registrarUsuario($_POST['username'], $_POST['password']);
}
?>
