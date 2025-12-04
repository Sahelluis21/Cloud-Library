<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cloud Library</title>

    {{-- Link CSS --}}
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <div class="login-container">
        <div class="brand-container">
            <h1 class="brand-name">Cloud Library</h1>
        </div>

        @if (!empty($error))
            <p class="alert">{{ $error }}</p>
        @endif

        <form method="POST" action="{{ url('/login') }}">
            @csrf
            <input class="form-control" type="text" name="username" placeholder="Usuário" required>
            <input class="form-control" type="password" name="password" placeholder="Senha" required>
            <button type="submit" class="btn-login">Entrar</button>
        </form>

        <form method="GET" action="{{ url('/register') }}">
            <button type="submit" class="btn-login" style="background-color:#374151;">Cadastrar</button>
        </form>
    </div>
</body>
</html>
