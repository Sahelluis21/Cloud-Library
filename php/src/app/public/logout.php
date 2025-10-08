<?php
require __DIR__ . '/../controller/authcontroller.php'; // inclui controller

$auth = new AuthController();
$auth->logout(); // chama logout do controller
