<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire Infos Personnelles</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Informations Personnelles</h1>

        <!-- Affichage des erreurs de validation -->
        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded-md mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Affichage d'un message de succès -->
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-md mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="/subscription" method="POST" class="space-y-4">
            @csrf <!-- Ajout de la protection CSRF -->

            <div>
                <label class="block text-gray-700">Nom</label>
                <input type="text" name="firstName" placeholder="Entrez votre nom" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('nom') }}">
            </div>

            <div>
                <label class="block text-gray-700">Prénom</label>
                <input type="text" name="lastName" placeholder="Entrez votre prénom" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('prenom') }}">
            </div>

            <div>
                <label class="block text-gray-700">Couleur des yeux</label>
                <input type="text" name="eyes" placeholder="Ex: Marron, Bleu" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('yeux') }}">
            </div>

            <div>
                <label class="block text-gray-700">Couleur de peau</label>
                <input type="text" name="skin" placeholder="Ex: Claire, Foncée" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('peau') }}">
            </div>
            <div>
                <label class="block text-gray-700">Couleur de cheveux</label>
                <input type="text" name="hair" placeholder="Ex: Claire, Foncée" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('peau') }}">
            </div>

            <div>
                <label class="block text-gray-700">Ville</label>
                <input type="text" name="address" placeholder="Entrez votre ville" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('ville') }}">
            </div>

            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600">
                Envoyer
            </button>
        </form>
    </div>

</body>
</html>
