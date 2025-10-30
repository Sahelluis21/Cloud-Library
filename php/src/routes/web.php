<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;

Route::get('/home', [HomeController::class, 'index'])->middleware('auth')->name('home');
Route::post('/upload', [HomeController::class, 'upload'])->name('upload');
Route::get('/login', [AuthController::class, 'showlogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::delete('/files/{id}', [HomeController::class, 'delete'])->name('files.delete');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/files/share/{id}', [App\Http\Controllers\HomeController::class, 'share'])->name('files.share');


Route::get('/dashboard', function() {
    return view('dashboard');
})->middleware('auth');
Route::get('/', function () {
    return view('welcome');
});
