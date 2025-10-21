<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login'); // chama a view login.blade.php
    }

    public function login(Request $request)
    {
        
        return $request->all();
    }
}
