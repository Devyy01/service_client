<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme {
            --color-primary: #1CAB94;
        }
    </style>
    <title>Analyse Ethnique</title>
</head>

<body>
    <header id="header" class="bg-primary fixed w-full z-20 top-0 start-0">
        <nav class="mx-auto flex max-w-7xl items-center justify-between p-3 lg:py-4 lg:px-8">
            <div class="flex lg:flex-1">
                <a href="https://www.quickdna.com" target="_blank" class="-m-1.5 p-1.5">
                    <img class="w-auto h-20" src="{{ asset('assets/images/QuickDNA_Logo.png') }}" alt="Logo">
                </a>
            </div>
        </nav>
    </header>
    <main>
        <div class="bg-white px-6 py-6 sm:py-10 lg:px-8">
            <div class="absolute inset-x-0 top-[-10rem] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[-20rem]"
                aria-hidden="true">
                <div class="relative left-1/2 -z-10 aspect-1155/678 w-[36.125rem] max-w-none -translate-x-1/2 rotate-[30deg] bg-linear-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%-40rem)] sm:w-[72.1875rem]"
                    style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)">
                </div>
            </div>
            <div class="mx-auto max-w-7xl text-center">
                <h2 class="text-4xl text-bold tracking-tight text-balance text-gray-900 sm:text-5xl">Générez votre
                    Rapport Ethnique</h2>
                <p class="mt-2 text-lg/8 text-gray-600">--------------------------------------------------------------
                </p>
            </div>
            <div class="mx-auto mt-10 max-w-4xl sm:mt-14">
                <div class="grid grid-cols-1 gap-x-8 gap-y-5 sm:grid-cols-2">
                    <div>
                        <label for="firstname" class="block text-sm sm:text-base text-gray-900">Nom</label>
                        <input type="text" name="firstname" id="firstname" placeholder="Entrez votre nom"
                            class="block mt-1.5 w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 transition-all duration-200 focus:outline-2 focus:-outline-offset-2 focus:outline-primary" />
                    </div>
                    <div>
                        <label for="lastname" class="block text-sm sm:text-base text-gray-900">Prénom</label>
                        <input type="text" name="lastname" id="lastname" placeholder="Entrez votre prénom"
                            class="block mt-1.5 w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 transition-all duration-200 focus:outline-2 focus:-outline-offset-2 focus:outline-primary" />
                    </div>
                    <div>
                        <label for="address" class="block text-sm sm:text-base text-gray-900">Adresse</label>
                        <input type="text" name="address" id="address" placeholder="Entrez votre adresse"
                            class="block mt-1.5 w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 transition-all duration-200 focus:outline-2 focus:-outline-offset-2 focus:outline-primary" />
                    </div>
                    <div>
                        <label for="city" class="block text-sm sm:text-base text-gray-900">Ville</label>
                        <input type="text" name="city" id="city" placeholder="Entrez votre ville"
                            class="block mt-1.5 w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 transition-all duration-200 focus:outline-2 focus:-outline-offset-2 focus:outline-primary" />
                    </div>
                    <div>
                        <label for="postal_code" class="block text-sm sm:text-base text-gray-900">Code postal</label>
                        <input type="text" name="postal_code" id="postal_code" placeholder="Entrez votre code postal"
                            class="block mt-1.5 w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 transition-all duration-200 focus:outline-2 focus:-outline-offset-2 focus:outline-primary" />
                    </div>
                    <div>
                        <label for="country" class="block text-sm sm:text-base text-gray-900">Pays</label>
                        <select id="country"
                            class="block mt-1.5 w-full rounded-md bg-white px-3.5 py-2.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 transition-all duration-200 focus:outline-2 focus:-outline-offset-2 focus:outline-primary">
                            <option value="" disabled>Choisissez un pays</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country['label'] }}">
                                    {{ $country['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="characteristic"
                            class="block text-sm sm:text-base text-gray-900">Caractéristique</label>
                        <textarea name="characteristic" id="characteristic" placeholder="Décrivez vos caractéristiques" rows="4"
                            class="block mt-1.5 w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 transition-all duration-200 focus:outline-2 focus:-outline-offset-2 focus:outline-primary"></textarea>
                    </div>
                </div>
                <div class="mt-10">
                    <button type="button" id="generate"
                        class="flex justify-center items-center gap-2 cursor-pointer w-full rounded-md ring-1 ring-primary px-3.5 py-2.5 text-center text-sm sm:text-base text-bold text-primary duration-200 transition-all hover:text-white shadow-xs hover:bg-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                        <div id="spinner"
                            class="hidden w-5 h-5 border-3 border-gray-200 border-t-3 border-t-primary rounded-full animate-spin">
                        </div> Génerer
                    </button>
                </div>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            function adjustMainPadding() {
                const headerHeight = $('#header').outerHeight();
                $('main').css('padding-top', headerHeight + 'px');
            }

            adjustMainPadding();
            $(window).resize(adjustMainPadding);

            const infos = {
                firstName: null,
                lastName: null,
                address: null,
                city: null,
                postal_code: null,
                country: null,
                characteristic: null
            };

            $('#firstname').on('input', function() {
                infos.firstName = $(this).val();
            });

            $('#lastname').on('input', function() {
                infos.lastName = $(this).val();
            });

            $('#address').on('input', function() {
                infos.address = $(this).val();
            });

            $('#city').on('input', function() {
                infos.city = $(this).val();
            });

            $('#postal_code').on('input', function() {
                infos.postal_code = $(this).val();
            });

            $('#country').on('change', function() {
                infos.country = $(this).val();
            });

            $('#characteristic').on('input', function() {
                infos.characteristic = $(this).val();
            });

            $('#generate').on('click', function() {

                const $btn = $(this);
                const $spinner = $btn.find('#spinner');

                $spinner.removeClass('hidden');
                $btn
                    .prop('disabled', true)
                    .addClass('cursor-not-allowed bg-primary/60 text-white')
                    .removeClass('cursor-pointer text-primary hover:bg-primary hover:text-white');


                $.ajax({
                    url: '/subscription',
                    method: 'POST',
                    data: infos,
                    // xhrFields: {
                    //     responseType: 'blob'
                    // },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(blob) {
                        toastr.success('Génération votre rapport éthnique a été succée.',
                            'Success');
                        $spinner.addClass('hidden');
                        $btn
                            .prop('disabled', false)
                            .removeClass('cursor-not-allowed bg-primary/60 text-white')
                            .addClass(
                                'cursor-pointer text-primary hover:bg-primary hover:text-white'
                            );
                        console.log('Données du formulaire :', infos);
                        window.location.href = '/generatePdf';
                    },
                    error: function() {
                        toastr.error('Une erreur est survenue. Veuillez réessayer.', 'Erreur');
                    },
                });
            });
        });
    </script>
</body>

</html>
