<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showLogin(Request $request)
    {
        // Si déjà authentifié, redirige directement
        if ($request->session()->get('authenticated')) {
            return redirect()->intended('/');
        }

        // Si formulaire POST
        if ($request->isMethod('post')) {
            $password = $request->input('password');
            if (password_verify($password, env('SECURE_PASSWORD'))) {
                $request->session()->put('authenticated', true);
                return redirect()->intended('/'); 
            } else {
                return back()->with('error', 'Mot de passe incorrect');
            }
        }

        // Affiche la vue login
        return view('login');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('authenticated'); // Supprime la session
        $request->session()->invalidate();            // Invalide la session
        $request->session()->regenerateToken();       // Regénère le token CSRF

        return redirect('/login')->with('message', 'Déconnexion réussie');
    }
}
