<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirecionamento se logado
if(isset($_SESSION['user_id'])) {
    header("Location: /home/index.php");
    exit;
}

require __DIR__.'/config/db_connect.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Por favor, preencha todos os campos";
    } else {
        try {
            error_log("Tentativa de login com usuário: " . $username);
            
            $stmt = $conn->prepare("SELECT id, username, password_hash FROM users WHERE username = :username LIMIT 1");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            
            if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
                error_log("Usuário encontrado: " . print_r($user, true));
                
                if (password_verify($password, $user['password_hash'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    session_regenerate_id(true);
                    header("Location: ../home/index.php");
                    exit;
                } else {
                    error_log("Falha na verificação da senha para usuário: " . $username);
                }
            } else {
                error_log("Usuário não encontrado: " . $username);
            }
            
            $error = "Credenciais inválidas";
            
        } catch (PDOException $e) {
            error_log("ERRO DE LOGIN: " . $e->getMessage());
            $error = "Erro no sistema. Por favor, tente novamente mais tarde.";
            
            if (ini_get('display_errors')) {
                $error .= "<br>Detalhes: " . htmlspecialchars($e->getMessage());
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Cloud Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../frontend/login.css" />
</head>
<body>
    <div class="login-container">
        <div class="brand-container">
            <div class="logo-circle">
                <img src="logos/lgo library.png" alt="logo" />
            </div>
            <h1 class="brand-name">Cloud Library</h1>
        </div>

        <?php if (isset($error) && $error !== null): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="mb-3">
                <input type="text" class="form-control" id="username" name="username" placeholder="Usuário" required />
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" id="password" name="password" placeholder="Senha" required />
            </div>
            <button type="submit" class="btn btn-login btn-primary w-100">Entrar</button>
        </form>
        
        <!-- Para cadastrar novos usuarios -->
        <div class="mt-3 text-center">
            <a href="home/registro.html" class="btn btn-login w-100">Cadastrar novo usuário</a>
        </div>
    </div>
</body>
</html>
