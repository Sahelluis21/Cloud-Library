<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 50px; }
        .container { max-width: 600px; margin: auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        a { color: #3490dc; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bem-vindo, {{ auth()->user()->username }}!</h1>
        <p>Login realizado com sucesso.</p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Sair</button>
        </form>
    </div>
</body>
</html>
