<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Cloud Library</title>
     <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body class="login-page">

    <div class="login-container">
        <h2>Criar Conta</h2>

        @if ($errors->any())
            <div class="error-box">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
    @csrf

            <div class="form-group">
                <label for="username">Nome de usuário</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="apelido">Apelido</label>
                <input type="text" id="apelido" name="apelido" placeholder="Como você quer ser chamado?">
            </div>

            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmar Senha</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>

            <button type="submit" class="btn-primary">Cadastrar</button>

            <p class="login-link">Já tem conta? <a href="{{ route('login') }}">Entrar</a></p>
        </form>

    </div>

</body>
</html>
