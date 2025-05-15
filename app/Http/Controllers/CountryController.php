<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class CountryController extends Controller
{
    public function showCountryForm()
    {
        $countries = json_decode(File::get(public_path('assets/files/countries/countries.json')), true);

        $countries = collect($countries)
            ->sortBy('label')
            ->values()
            ->all();
        return view('acceuil.acceuil', compact('countries'));
    }
}
