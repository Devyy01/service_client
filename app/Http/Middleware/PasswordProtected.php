<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PasswordProtected
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Si l’utilisateur est déjà authentifié en session
        if ($request->session()->has('authenticated') && $request->session()->get('authenticated') === true) {
            return $next($request); // accès autorisé
        }

        // 2. Si le formulaire a été soumis (POST)
        
        if ($request->isMethod('post') && $request->has('password')) {
            if (password_verify($request->input('password'), env('SECURE_PASSWORD'))) {
                $request->session()->put('authenticated', true); // Authentification réussie
                // return redirect($request->url()); // Recharge la page
                return redirect()->intended('/');
            } else {
                return response('Mot de passe incorrect', 403);
            }
        }

        
        return response()->view('login');
    }
}

