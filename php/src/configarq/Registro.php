<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require __DIR__ . '/../model/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $senha_plain = $_POST['password'];

    if (empty($username) || empty($senha_plain)) {
        die('Usuário e senha são obrigatórios!');
    }

    // Verificar se o usuário já existe
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->fetch()) {
        die('Nome de usuário já está em uso.');
    }

    // Criar o hash da senha
    $hash = password_hash($senha_plain, PASSWORD_DEFAULT);

    // Inserir novo usuário no banco
    $stmt = $conn->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
    if ($stmt->execute([$username, $hash])) {
        echo "Usuário cadastrado com sucesso!";
         header("Location: ../login.php"); // Descomente para redirecionar
         exit;
    } else {
        echo "Erro ao cadastrar usuário.";
    }
} else {
    echo "Requisição inválida.";
}
?>
