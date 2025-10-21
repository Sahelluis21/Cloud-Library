<body>
    <div class="login-container">
        <div class="brand-container">
            <div class="logo-circle">
                <img src="../assets/logo.png" alt="Logo">
            </div>
            <h1 class="brand-name">Cloud Library</h1>
        </div>

        <?php if (!empty($error)): ?>
            <p class="alert"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="/login">
            <input class="form-control" type="text" name="username" placeholder="Usuário" required>
            <input class="form-control" type="password" name="password" placeholder="Senha" required>
            <button type="submit" class="btn-login">Entrar</button>
        </form>

        <form method="GET" action="/register">
            <button type="submit" class="btn-login">Cadastrar</button>
        </form>
    </div>
</body>