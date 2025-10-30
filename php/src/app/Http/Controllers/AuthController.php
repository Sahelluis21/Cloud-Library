<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Mostra a tela de login
    public function showLogin()
    {
        return view('auth.login'); 
    }
    // Faz login
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {

            $request->session()->regenerate();
            return redirect()->intended('/home');
        }

        return back()->withErrors([
            'username' => 'Credenciais Invalidas',
        ]);
    }


    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Processa o cadastro
    public function register(Request $request)
{
    // Validação
    $validated = $request->validate([
        'username' => 'required|string|max:100',
        'apelido'  => 'nullable|string|max:100',
        'email'    => 'required|email|unique:users',
        'password' => 'required|min:6|confirmed'
    ]);

    // Cria o novo usuário
    $user = User::create([
        'username'      => $validated['username'],
        'apelido'       => $validated['apelido'] ?? null, // se não informar, será null
        'email'         => $validated['email'],
        'password_hash' => Hash::make($validated['password']),
    ]);

    // Loga o usuário automaticamente
    auth()->login($user);

    return redirect()->route('home')->with('success', 'Cadastro realizado com sucesso!');
}



    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
