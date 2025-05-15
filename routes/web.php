<?php

use App\Http\Controllers\CountryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;


Route::get('/countries', function () {
    $path = resource_path('files/countries.json');
    $countries = File::get($path);
    return Response::make($countries, 200, ['Content-Type' => 'application/json']);
});

Route::get('/', [CountryController::class, 'showCountryForm'])->name('countries');
