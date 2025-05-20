<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Connexion Sécurisée</title>
    <style>
        body {
            background: #f4f4f4;
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .login-container img.logo {
            max-width: 120px;
            margin-bottom: 1rem;
        }

        .login-container h1 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .login-container input[type="password"],
        .login-container input[type="text"] {
            width: 100%;
            padding: 0.75rem;
            margin: 0.5rem 0 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            box-sizing: border-box;
        }

        .toggle-password {
            position: relative;
            display: inline-block;
            cursor: pointer;
            user-select: none;
            font-size: 0.9rem;
            color: #3490dc;
            margin-bottom: 1rem;
        }

        .login-container button {
            background: #3490dc;
            color: #fff;
            border: none;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        .login-container button:hover {
            background: #2779bd;
        }

        .error-message {
            color: red;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

    <div class="login-container">
        {{-- Logo --}}
        <img src="{{ asset('logo.png') }}" alt="Logo de l'application" class="logo" />

        {{-- Titre --}}
        <h1>Connexion Sécurisée</h1>

        {{-- Message d'erreur --}}
        @if(session('error'))
            <div class="error-message">{{ session('error') }}</div>
        @endif

        {{-- Formulaire --}}
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <label for="password" class="sr-only">Mot de passe</label>
            <input id="password" type="password" name="password" placeholder="Mot de passe" required autofocus />
            <div>
                <span class="toggle-password" onclick="togglePassword()">Afficher le mot de passe</span>
            </div>
            <button type="submit">Se connecter</button>
        </form>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const toggle = document.querySelector('.toggle-password');
            if (input.type === 'password') {
                input.type = 'text';
                toggle.textContent = 'Cacher le mot de passe';
            } else {
                input.type = 'password';
                toggle.textContent = 'Afficher le mot de passe';
            }
        }
    </script>

</body>
</html>
