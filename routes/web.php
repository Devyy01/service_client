<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PersonalInfoController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/subscription', [PersonalInfoController::class, 'showForm'])->name('subscription.form');
Route::post('/subscription', [PersonalInfoController::class, 'submitForm']);
Route::get('/login', [LoginController::class, 'showlogin'])->name('login');
Route::post('/login', [LoginController::class, 'LoginForm']);