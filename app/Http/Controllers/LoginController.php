<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showlogin()
    {
        return view('login'); // Charger la vue contenant ton formulaire
    }
    public function LoginForm(Request $request)
    {
        // Valider les données
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'yeux' => 'required|string|max:255',
            'peau' => 'required|string|max:255',
            'ville' => 'required|string|max:255',
        ]);

        // Traiter les données ici (ex: les enregistrer dans une base de données)
        // Pour cet exemple, on va juste les afficher
        dd($validated); // Affiche les données validées

        return redirect()->back()->with('success', 'Données envoyées avec succès!');
    }
}
