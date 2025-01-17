@extends('layouts.master', [
    'title' => 'Clients',
])

@push('haut')
    <link href="{{ asset('assets/table/css') }}/dataTables.tailwindcss.css" />
@endpush

@push('bas')
    <script src="{{ asset('assets/js') }}/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/table/js') }}/jquery-3.7.1.js"></script>
    <script src="{{ asset('assets/table/js') }}/dataTables.js"></script>
    <script src="{{ asset('assets/table/js') }}/dataTables.tailwindcss.js"></script>
    <link href="{{ asset('assets/table/css') }}/tailwindcss.css" />
    <script>
        $(document).ready(function() {
            $('#example').DataTable();
        });
        $('#example').DataTable({
            dom: "<'flex justify-between items-center'<'flex items-center'l><'flex items-center'f>>" +
                "<'mt-4'tr>" +
                "<'flex justify-between items-center'<'p-2'i><'p-2'p>>",

        });
    </script>
    <script>
        $(document).ready(function() {
            // On écoute l'événement de soumission pour chaque formulaire dans le foreach
            $('form[id^="formUpdate"]').on('submit', function(e) {
                e.preventDefault(); // Empêche l'envoi normal du formulaire

                var formId = $(this).attr('id'); // Récupère l'id du formulaire, par exemple 'formUpdate-1'
                var clientId = formId.split('-')[
                    1]; // Extrait l'id du client à partir de l'id du formulaire

                // Réinitialiser les messages d'erreur pour ce formulaire
                $('#formUpdate-' + clientId + ' .text-danger').text(''); // Réinitialiser toutes les erreurs
                $('#loadingMessage-' + clientId).show(); // Afficher le message "Veuillez patienter"

                var formData = $(this).serialize(); // Sérialiser les données du formulaire

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        // Masquer le message de chargement
                        $('#loadingMessage-' + clientId).hide();

                        // Si succès, afficher un message de succès
                        $('#successMessage-' + clientId).text('Mise à jour réussie !')
                            .show(); // Afficher un message de succès
                        $('#formUpdate-' + clientId)[0].reset(); // Réinitialiser le formulaire
                        location
                            .reload(); // Rafraîchissement de la page (ou vous pouvez choisir de faire un rafraîchissement partiel)
                    },
                    error: function(xhr) {
                        // Masquer le message de chargement
                        $('#loadingMessage-' + clientId).hide();

                        // Si une erreur de validation se produit, afficher les erreurs dans les éléments correspondants
                        var errors = xhr.responseJSON.errors;
                        for (var field in errors) {
                            // Assurez-vous de cibler chaque champ d'erreur de manière unique pour ce client
                            $('#error-' + field + '-' + clientId).text(errors[field][
                                0
                            ]); // Mettre à jour l'erreur avec l'id spécifique
                        }
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#formCreate').on('submit', function(e) {
                e.preventDefault(); // Empêche l'envoi normal du formulaire

                // Réinitialiser les messages d'erreur
                $('.text-danger').text('');
                $('#loadingButton').hide(); // Cacher le bouton
                $('#loadingMessage').show(); // Afficher le message "Veuillez patienter"

                // Créer un objet FormData pour gérer le fichier et les autres champs
                var formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false, // Ne pas traiter les données
                    contentType: false, // Ne pas définir de type de contenu (important pour l'envoi de fichiers)
                    success: function(response) {
                        // Masquer le message de chargement
                        $('#loadingMessage').hide();
                        $('#loadingButton').show();

                        // Si succès, afficher un message de succès
                        alert(response.success);
                        $('#formCreate')[0].reset(); // Réinitialiser le formulaire
                        location.reload(); // Rafraîchit la page
                    },
                    error: function(xhr) {
                        // Masquer le message de chargement
                        $('#loadingMessage').hide();
                        $('#loadingButton').show();

                        // Si une erreur de validation se produit, afficher les erreurs dans les éléments correspondants
                        var errors = xhr.responseJSON.errors;
                        for (var field in errors) {
                            $('#' + 'error-' + field).text(errors[field][0]);
                        }
                    }
                });
            });
        });
    </script>
@endpush

@section('content')
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        @include('layouts.status')

        <div class="flex items-center space-x-4 py-5 lg:py-6">
            <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                {{ __('messages.client') }}
            </h2>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-2 lg:gap-6">
            <div class="rounded-lg bg-gradient-to-r from-sky-400 to-blue-600 p-1">
                <div class="rounded-lg bg-slate-50 px-4 py-4 dark:bg-navy-900 sm:px-5">
                    <div>
                        <h2 class="text-lg font-medium tracking-wide text-slate-600 line-clamp-1 dark:text-navy-100">
                            {{ __('messages.nbreClient') }}
                        </h2>
                    </div>
                    <div class="pt-2">
                        <p>
                            {{ $nbreClient }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="rounded-lg bg-gradient-to-r from-sky-400 to-blue-600 p-1">
                <div class="rounded-lg bg-slate-50 px-4 py-4 dark:bg-navy-900 sm:px-5">
                    <div>
                        <h2 class="text-lg font-medium tracking-wide text-slate-600 line-clamp-1 dark:text-navy-100">
                            {{ __('messages.tStock') }}
                        </h2>
                    </div>
                    <div class="pt-2">
                        <p>
                            {{ $totalStock }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-2 lg:gap-6">
            <div class="rounded-lg bg-gradient-to-r from-sky-400 to-blue-600 p-1" style="background: lightcoral;">
                <a href="{{ url('retard', 2) }}">
                    <div class="rounded-lg bg-slate-50 px-4 py-4 dark:bg-navy-900 sm:px-5">
                        <div>
                            <h2 class="text-lg font-medium tracking-wide text-slate-600 line-clamp-1 dark:text-navy-100">
                                {{ __('messages.semaine') }}
                            </h2>
                        </div>
                        <div class="pt-2">
                            <p>
                                {{ $clientsWithoutStockLastWeek }}
                            </p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="rounded-lg bg-gradient-to-r from-sky-400 to-blue-600 p-1" style="background: lightcoral;">
                <a href="{{ url('retard', 1) }}">
                    <div class="rounded-lg bg-slate-50 px-4 py-4 dark:bg-navy-900 sm:px-5">
                        <div>
                            <h2 class="text-lg font-medium tracking-wide text-slate-600 line-clamp-1 dark:text-navy-100">
                                {{ __('messages.mois') }}
                            </h2>
                        </div>
                        <div class="pt-2">
                            <p>
                                {{ $clientsWithoutStockLastMonth }}
                            </p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <br>
        <br>

        <div class="grid grid-cols-1 gap-4 sm:gap-5 lg:gap-6">
            @if (Auth::user()->type !== 'division')
                <div class="card px-4 pb-4 sm:px-5">
                    <div class="my-3 flex h-8 items-center justify-between px-4 sm:px-5">
                        <h2 class="font-medium tracking-wide text-slate-700 line-clamp-1 dark:text-navy-100 lg:text-base">

                        </h2>
                        <div x-data="usePopper({ placement: 'bottom-end', offset: 4 })" @click.outside="isShowPopper &amp;&amp; (isShowPopper = false)"
                            class="inline-flex">
                            <div x-data="{ showModal: false }">
                                <button @click="showModal = true"
                                    class="btn relative bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                                    {{ __('messages.add') }}
                                </button>
                                <template x-teleport="#x-teleport-target">
                                    <div class="fixed inset-0 z-[100] flex flex-col items-center justify-center overflow-hidden px-4 py-6 sm:px-5"
                                        x-show="showModal" role="dialog" @keydown.window.escape="showModal = false">
                                        <div class="absolute inset-0 bg-slate-900/60 transition-opacity duration-300"
                                            @click="showModal = false" x-show="showModal" x-transition:enter="ease-out"
                                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                            x-transition:leave="ease-in" x-transition:leave-start="opacity-100"
                                            x-transition:leave-end="opacity-0">
                                        </div>
                                        <div class="relative w-full max-w-lg origin-top rounded-lg bg-white transition-all duration-300 dark:bg-navy-700"
                                            x-show="showModal" x-transition:enter="easy-out"
                                            x-transition:enter-start="opacity-0 scale-95"
                                            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="easy-in"
                                            x-transition:leave-start="opacity-100 scale-100"
                                            x-transition:leave-end="opacity-0 scale-95">
                                            <div
                                                class="flex justify-between rounded-t-lg bg-slate-200 px-4 py-3 dark:bg-navy-800 sm:px-5">
                                                <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
                                                    {{ __('messages.add') }}
                                                </h3>
                                                <button @click="showModal = !showModal"
                                                    class="btn -mr-1.5 size-7 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <form id="formCreate" action="{{ route('clients.store') }}" method="POST"
                                                role="form" enctype="multipart/form-data">
                                                @csrf
                                                <div class="px-4 py-4 sm:px-5">
                                                    <label class="block">
                                                        <span>{{ __('messages.file') }}</span><br>
                                                        <small><em>{{ __('messages.click') }}</em></small>
                                                        <input name="fichier"
                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                            placeholder="Veuillez sélectionner le fichier" type="file"
                                                            accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
                                                        <span style="color: red;" id="error-fichier"></span>
                                                    </label>
                                                    <br>
                                                    <hr>
                                                    <div class="text-center"><strong>{{ __('messages.or') }}</strong></div>
                                                    <br>
                                                    <hr>
                                                    <label class="block">
                                                        <span>Division</span>
                                                        <select name="division"
                                                            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent">
                                                            <option value="">{{ __('messages.select') }}</option>
                                                            @foreach ($division as $item)
                                                                <option value="{{ $item->id }}">{{ $item->libelle }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <span style="color: red;" id="error-division"></span>
                                                    </label>
                                                    <label class="block">
                                                        <span>{{ __('messages.username') }}</span>
                                                        <input name="username"
                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                            placeholder="{{ __('messages.username') }}" type="text" />
                                                        <span style="color: red;" id="error-username"></span>
                                                    </label>
                                                    <label class="block">
                                                        <span>Code</span>
                                                        <input name="code"
                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                            placeholder="Code" type="text" />
                                                        <span style="color: red;" id="error-code"></span>
                                                    </label>
                                                    <label class="block">
                                                        <span>{{ __('messages.name') }}</span>
                                                        <input name="name"
                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                            placeholder="{{ __('messages.name') }}" type="text" />
                                                        <span style="color: red;" id="error-name"></span>
                                                    </label>
                                                    <label class="block">
                                                        <span>E-mail</span>
                                                        <input name="email"
                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                            placeholder="Email" type="email" />
                                                        <span style="color: red;" id="error-email"></span>
                                                    </label>
                                                    <label class="block">
                                                        <span>{{ __('messages.password') }}</span>
                                                        <input name="password"
                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                            placeholder="{{ __('messages.password') }}"
                                                            type="password" />
                                                        <span style="color: red;" id="error-password"></span>
                                                    </label>
                                                    <label class="block">
                                                        <span>{{ __('messages.pays') }}</span>
                                                        <select name="pays"
                                                            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent">
                                                            <option value="">{{ __('messages.select') }}</option>
                                                            @foreach ($pays as $pay)
                                                                <option value="{{ $pay->id }}">
                                                                    {{ $pay->libelle_pays }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <span style="color: red;" id="error-pays"></span>
                                                    </label>
                                                    <label class="block">
                                                        <span>{{ __('messages.status') }}</span>
                                                        <select id="statut" name="statut"
                                                            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent">
                                                            <option value="">
                                                                {{ __('messages.select') }}</option>
                                                            <option value="1">Active
                                                            </option>
                                                            <option value="0">
                                                                {{ __('messages.disable') }}</option>
                                                        </select>
                                                        <span style="color: red;" id="error-statut"></span>
                                                    </label>
                                                    <br>
                                                    <div class="space-x-2 text-right">
                                                        <div id="loadingMessage" style="display: none;">
                                                            <p style="color: #018ea9">{{ __('messages.warning') }}</p>
                                                        </div>
                                                        <div id="loadingButton">
                                                            <button type="submit"
                                                                class="btn min-w-[7rem] rounded-full bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                                                                {{ __('messages.add') }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card px-4 pb-4 sm:px-5">
                <div class="mt-5">
                    <div class="is-scrollbar-hidden min-w-full overflow-x-auto">
                        <table id="example" class="display">
                            <thead>
                                <tr>
                                    <th style="background: #018ea9; color: white;"
                                        class="whitespace-nowrap bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                        {{ __('messages.name') }}
                                    </th>
                                    <th style="background: #018ea9; color: white;"
                                        class="whitespace-nowrap bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                        Code
                                    </th>
                                    <th style="background: #018ea9; color: white;"
                                        class="whitespace-nowrap bg-slate-200 px-3 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                        {{ __('messages.pays') }}
                                    </th>
                                    <th style="background: #018ea9; color: white;"
                                        class="whitespace-nowrap bg-slate-200 px-3 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                        Division
                                    </th>
                                    <th style="background: #018ea9; color: white;"
                                        class="whitespace-nowrap bg-slate-200 px-3 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                        {{ __('messages.status') }}
                                    </th>
                                    <th style="background: #018ea9; color: white;"
                                        class="whitespace-nowrap bg-slate-200 px-3 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                        Stocks
                                    </th>
                                    <th style="background: #018ea9; color: white;"
                                        class="whitespace-nowrap bg-slate-200 px-3 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                        Date create
                                    </th>
                                    <th style="background: #018ea9; color: white;"
                                        class="whitespace-nowrap bg-slate-200 px-3 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                        Date update
                                    </th>
                                    <th style="background: #018ea9; color: white;"
                                        class="whitespace-nowrap rounded-r-lg bg-slate-200 px-3 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clients as $liste)
                                    <tr>
                                        <td class="whitespace-nowrap rounded-l-lg px-4 py-3 sm:px-5">
                                            {{ $liste->name_client }}
                                        </td>
                                        <td class="whitespace-nowrap rounded-l-lg px-4 py-3 sm:px-5">
                                            {{ $liste->code_client }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                            {{ $liste->libelle_pays }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                            <div
                                                class="badge bg-primary text-white shadow-soft shadow-primary/50 dark:bg-accent dark:shadow-accent/50">
                                                {{ $liste->libelle }}
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                            @if ($liste->status_client == 1)
                                                <div
                                                    class="badge bg-success text-white shadow-soft shadow-success/50 dark:bg-accent dark:shadow-accent/50">
                                                    Active
                                                </div>
                                            @endif
                                            @if ($liste->status_client == 0)
                                                <div
                                                    class="badge bg-error text-white shadow-soft shadow-error/50 dark:bg-accent dark:shadow-accent/50">
                                                    {{ __('messages.disable') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap rounded-r-lg px-4 py-3 sm:px-5">
                                            {{ $liste->sommeQuantiteInitiale }}
                                        </td>
                                        <td class="whitespace-nowrap rounded-r-lg px-4 py-3 sm:px-5">
                                            {{ $liste->created_at }}
                                        </td>
                                        <td class="whitespace-nowrap rounded-r-lg px-4 py-3 sm:px-5">
                                            {{ $liste->updated_at }}
                                        </td>
                                        <td data-column-id="actions" class="gridjs-td">
                                            <span>
                                                <div class="flex justify-center space-x-2">
                                                    <a href="{{ route('clients.show', $liste->id) }}"
                                                        class="btn size-8 p-0 text-slate-600 hover:bg-info/20 focus:bg-info/20 active:bg-info/25">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    @if (Auth::user()->type == 'admin')
                                                        <div x-data="{ showModal{{ $liste->id }}: false }">
                                                            <button @click="showModal{{ $liste->id }} = true"
                                                                class="btn size-8 p-0 text-info hover:bg-info/20 focus:bg-info/20 active:bg-info/25">
                                                                <i class="fa fa-edit"></i>
                                                            </button>
                                                            <template x-teleport="#x-teleport-target">
                                                                <div class="fixed inset-0 z-[100] flex flex-col items-center justify-center overflow-hidden px-4 py-6 sm:px-5"
                                                                    x-show="showModal{{ $liste->id }}" role="dialog"
                                                                    @keydown.window.escape="showModal{{ $liste->id }} = false">
                                                                    <div class="absolute inset-0 bg-slate-900/60 transition-opacity duration-300"
                                                                        @click="showModal{{ $liste->id }} = false"
                                                                        x-show="showModal{{ $liste->id }}"
                                                                        x-transition:enter="ease-out"
                                                                        x-transition:enter-start="opacity-0"
                                                                        x-transition:enter-end="opacity-100"
                                                                        x-transition:leave="ease-in"
                                                                        x-transition:leave-start="opacity-100"
                                                                        x-transition:leave-end="opacity-0"></div>
                                                                    <div class="relative w-full max-w-lg origin-top rounded-lg bg-white transition-all duration-300 dark:bg-navy-700"
                                                                        x-show="showModal{{ $liste->id }}"
                                                                        x-transition:enter="easy-out"
                                                                        x-transition:enter-start="opacity-0 scale-95"
                                                                        x-transition:enter-end="opacity-100 scale-100"
                                                                        x-transition:leave="easy-in"
                                                                        x-transition:leave-start="opacity-100 scale-100"
                                                                        x-transition:leave-end="opacity-0 scale-95">
                                                                        <div
                                                                            class="flex justify-between rounded-t-lg bg-slate-200 px-4 py-3 dark:bg-navy-800 sm:px-5">
                                                                            <h3
                                                                                class="text-base font-medium text-slate-700 dark:text-navy-100">
                                                                                {{ __('messages.edit') }}
                                                                            </h3>
                                                                            <button
                                                                                @click="showModal{{ $liste->id }} = !showModal{{ $liste->id }}"
                                                                                class="btn -mr-1.5 size-7 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    class="size-4.5" fill="none"
                                                                                    viewBox="0 0 24 24"
                                                                                    stroke="currentColor"
                                                                                    stroke-width="2">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        d="M6 18L18 6M6 6l12 12"></path>
                                                                                </svg>
                                                                            </button>
                                                                        </div>
                                                                        <form id="formUpdate-{{ $liste->id }}"
                                                                            action="{{ route('clients.update', $liste->id) }}"
                                                                            method="POST" role="form">
                                                                            @csrf
                                                                            @method('PATCH')
                                                                            <div class="px-4 py-4 sm:px-5">
                                                                                <div class="mt-4 space-y-4">
                                                                                    <label class="block">
                                                                                        <span>Division</span>
                                                                                        <select id="division"
                                                                                            name="division" required
                                                                                            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent">
                                                                                            <option
                                                                                                value="{{ $liste->division_id }}">
                                                                                                {{ $liste->libelle }}
                                                                                            </option>
                                                                                            @foreach ($division as $item)
                                                                                                <option
                                                                                                    value="{{ $item->id }}">
                                                                                                    {{ $item->libelle }}
                                                                                                </option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                        <span style="color: red;"
                                                                                            id="error-division-{{ $liste->id }}"></span>
                                                                                    </label>
                                                                                    <label class="block">
                                                                                        <span>{{ __('messages.username') }}</span>
                                                                                        <input id="username"
                                                                                            name="username" required
                                                                                            value="{{ $liste->username }}"
                                                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                                                            placeholder="{{ __('messages.username') }}"
                                                                                            type="text" />
                                                                                        <span style="color: red;"
                                                                                            id="error-username-{{ $liste->id }}"></span>
                                                                                    </label>
                                                                                    <label class="block">
                                                                                        <span>{{ __('messages.name') }}</span>
                                                                                        <input id="nom"
                                                                                            name="nom"
                                                                                            value="{{ $liste->name_client }}"
                                                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                                                            placeholder="{{ __('messages.name') }}"
                                                                                            type="text" />
                                                                                        <span style="color: red;"
                                                                                            id="error-nom-{{ $liste->id }}"></span>

                                                                                    </label>
                                                                                    <label class="block">
                                                                                        <span>Code</span>
                                                                                        <input id="code"
                                                                                            name="code" required
                                                                                            value="{{ $liste->code_client }}"
                                                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                                                            placeholder="Code"
                                                                                            type="text" />
                                                                                        <span style="color: red;"
                                                                                            id="error-code-{{ $liste->id }}"></span>
                                                                                    </label>
                                                                                    <label class="block">
                                                                                        <span>E-mail</span>
                                                                                        <input id="email"
                                                                                            name="email"
                                                                                            value="{{ $liste->email_client }}"
                                                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                                                            placeholder="Email"
                                                                                            type="email" />
                                                                                        <span style="color: red;"
                                                                                            id="error-email-{{ $liste->id }}"></span>
                                                                                    </label>
                                                                                    <label class="block">
                                                                                        <span>{{ __('messages.password') }}</span>
                                                                                        <input id="password"
                                                                                            name="password"
                                                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                                                            placeholder="{{ __('messages.password') }}"
                                                                                            type="password" />
                                                                                    </label>
                                                                                    <label class="block">
                                                                                        <span>{{ __('messages.pays') }}</span>
                                                                                        <select id="pays"
                                                                                            name="pays" required
                                                                                            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent">
                                                                                            <option
                                                                                                value="{{ $liste->pays_id }}">
                                                                                                {{ $liste->libelle_pays }}
                                                                                            </option>
                                                                                            @foreach ($pays as $pay)
                                                                                                <option
                                                                                                    value="{{ $pay->id }}">
                                                                                                    {{ $pay->libelle_pays }}
                                                                                                </option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                        <span style="color: red;"
                                                                                            id="error-pays-{{ $liste->id }}"></span>
                                                                                    </label>
                                                                                    <label class="block">
                                                                                        <span>{{ __('messages.status') }}</span>
                                                                                        <select id="statut"
                                                                                            name="statut"
                                                                                            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent">
                                                                                            <option
                                                                                                value="{{ $liste->status_client }}">
                                                                                                {{ $liste->status_client == 1 ? 'Active' : 'Désactive' }}
                                                                                            </option>
                                                                                            <option value="1">Active
                                                                                            </option>
                                                                                            <option value="0">
                                                                                                {{ __('messages.disable') }}
                                                                                            </option>
                                                                                        </select>
                                                                                        <span style="color: red;"
                                                                                            id="error-statut-{{ $liste->id }}"></span>
                                                                                    </label>
                                                                                    <div id="loadingMessage"
                                                                                        style="display: none;">
                                                                                        <p>{{ __('messages.warning') }}</p>
                                                                                    </div>
                                                                                    <!-- Message de succès -->
                                                                                    <div id="successMessage-{{ $liste->id }}"
                                                                                        style="color: green; font-weight: bold; display: none;">
                                                                                    </div>
                                                                                    <div class="space-x-2 text-right">
                                                                                        <button type="submit"
                                                                                            class="btn min-w-[7rem] rounded-full bg-info font-medium text-white hover:bg-info-focus focus:bg-info-focus active:bg-info-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                                                                                            {{ __('messages.edit') }}
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                        <div x-data="{ showModalDelete{{ $liste->id }}: false }">
                                                            <button @click="showModalDelete{{ $liste->id }} = true"
                                                                class="btn size-8 p-0 text-error hover:bg-error/20 focus:bg-error/20 active:bg-error/25">
                                                                <i class="fa fa-trash-alt"></i>
                                                            </button>
                                                            <template x-teleport="#x-teleport-target">
                                                                <div class="fixed inset-0 z-[100] flex flex-col items-center justify-center overflow-hidden px-4 py-6 sm:px-5"
                                                                    x-show="showModalDelete{{ $liste->id }}"
                                                                    role="dialog"
                                                                    @keydown.window.escape="showModalDelete{{ $liste->id }} = false">
                                                                    <div class="absolute inset-0 bg-slate-900/60 transition-opacity duration-300"
                                                                        @click="showModalDelete{{ $liste->id }} = false"
                                                                        x-show="showModalDelete{{ $liste->id }}"
                                                                        x-transition:enter="ease-out"
                                                                        x-transition:enter-start="opacity-0"
                                                                        x-transition:enter-end="opacity-100"
                                                                        x-transition:leave="ease-in"
                                                                        x-transition:leave-start="opacity-100"
                                                                        x-transition:leave-end="opacity-0"></div>
                                                                    <div class="relative w-full max-w-lg origin-top rounded-lg bg-white transition-all duration-300 dark:bg-navy-700"
                                                                        x-show="showModalDelete{{ $liste->id }}"
                                                                        x-transition:enter="easy-out"
                                                                        x-transition:enter-start="opacity-0 scale-95"
                                                                        x-transition:enter-end="opacity-100 scale-100"
                                                                        x-transition:leave="easy-in"
                                                                        x-transition:leave-start="opacity-100 scale-100"
                                                                        x-transition:leave-end="opacity-0 scale-95">
                                                                        <div
                                                                            class="flex justify-between rounded-t-lg bg-slate-200 px-4 py-3 dark:bg-navy-800 sm:px-5">
                                                                            <h3
                                                                                class="text-base font-medium text-slate-700 dark:text-navy-100">
                                                                                {{ __('messages.delete') }}
                                                                            </h3>
                                                                            <button
                                                                                @click="showModalDelete{{ $liste->id }} = !showModalDelete{{ $liste->id }}"
                                                                                class="btn -mr-1.5 size-7 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    class="size-4.5" fill="none"
                                                                                    viewBox="0 0 24 24"
                                                                                    stroke="currentColor"
                                                                                    stroke-width="2">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        d="M6 18L18 6M6 6l12 12"></path>
                                                                                </svg>
                                                                            </button>
                                                                        </div>
                                                                        <form
                                                                            action="{{ route('clients.destroy', $liste->id) }}"
                                                                            method="POST" role="form">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <div class="px-4 py-4 sm:px-5">
                                                                                <div class="mt-4 space-y-4">
                                                                                    <label class="block">
                                                                                        {{ __('messages.sure') }}
                                                                                    </label>
                                                                                    <div class="space-x-2 text-right">
                                                                                        <button type="submit"
                                                                                            class="btn min-w-[7rem] rounded-full bg-error font-medium text-white hover:bg-error-focus focus:bg-error-focus active:bg-error-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                                                                                            {{ __('messages.delete') }}
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    @endif
                                                </div>
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
