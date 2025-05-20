<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\LoginController;
use App\Http\Middleware\PasswordProtected;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\PersonalInfoController;

Route::get('/countries', function () {
    $path = resource_path('files/countries.json');
    $countries = File::get($path);
    return Response::make($countries, 200, ['Content-Type' => 'application/json']);
});

Route::match(['get', 'post'], '/login', [LoginController::class, 'showLogin'])->name('login');

Route::middleware(['password.protected'])->group(function () {
    
    Route::get('/', [CountryController::class, 'showCountryForm'])->name('countries');
    Route::get('/generatePdf', [PersonalInfoController::class, 'generatePdf']);
    Route::post('/subscription', [PersonalInfoController::class, 'submitForm']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
   
});