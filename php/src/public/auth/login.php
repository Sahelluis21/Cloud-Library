<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Cloud Library</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h2>Login</h2>
    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST" action="/login">
        <label>Usuário:</label>
        <input type="text" name="username" required>
        <br>
        <label>Senha:</label>
        <input type="password" name="password" required>
        <br>
        <button type="submit">Entrar</button>
    </form>

    <form method="GET" action="/register" style="margin-top: 10px;">
        <button type="submit">Cadastrar</button>
    </form>
</body>
</html>
