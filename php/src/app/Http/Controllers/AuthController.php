<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login'); // sua view login.blade.php
    }

    public function login(Request $request)
    {
        // lógica de login
    }

    public function logout(Request $request)
    {
        // logout
    }
}
