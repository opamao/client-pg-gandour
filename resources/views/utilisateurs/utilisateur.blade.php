@extends('layouts.master', [
    'title' => 'Utilisateurs',
])

@push('haut')
    <link href="{{ asset('assets/table/css') }}/dataTables.tailwindcss.css" />
@endpush

@push('bas')
    <script src="{{ asset('assets/table/js') }}/jquery-3.7.1.js"></script>
    {{-- <link href="{{ asset('assets/table/css') }}/tailwindcss.css" /> --}}
    <script src="{{ asset('assets/table/js') }}/dataTables.js"></script>
    <script src="{{ asset('assets/table/js') }}/dataTables.tailwindcss.js"></script>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                {{ __('messages.user') }}
            </h2>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:gap-5 lg:gap-6">
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
                                        x-transition:leave-end="opacity-0"></div>
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
                                        <form id="formCreate" action="{{ route('utilisateurs.store') }}" method="POST"
                                            role="form" enctype="multipart/form-data">
                                            @csrf
                                            <div class="px-4 py-4 sm:px-5">
                                                <div class="mt-4 space-y-4">
                                                    <label class="block">
                                                        <span>{{ __('messages.file') }}</span><br>
                                                        <small><em>{{ __('messages.click') }}</em></small>
                                                        <input name="fichier" id="fichier"
                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                            placeholder="Veuillez sélectionner le fichier" type="file"
                                                            accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
                                                        <span style="color: red;" id="error-fichier"></span>
                                                    </label>
                                                    <br>
                                                    <hr>
                                                    <div class="text-center"><strong>{{ __('messages.or') }}</strong></div>
                                                    <hr>
                                                    <label class="block">
                                                        <span>Division</span>
                                                        <select name="division[]" x-init="$el._tom = new Tom($el)"
                                                            class="mt-1.5 w-full" multiple
                                                            placeholder="{{ __('messages.select') }}..."
                                                            autocomplete="off">
                                                            <option value="">{{ __('messages.select') }}...</option>
                                                            @foreach ($division as $item)
                                                                <option value="{{ $item->id }}">{{ $item->libelle }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <span style="color: red;" id="error-division"></span>
                                                    </label>
                                                    <label class="block">
                                                        <span>{{ __('messages.name') }}</span>
                                                        <input name="name" id="name"
                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                            placeholder="{{ __('messages.name') }}" type="text" />
                                                        <span style="color: red;" id="error-name"></span>
                                                    </label>
                                                    <label class="block">
                                                        <span>E-mail</span>
                                                        <input name="email" id="email"
                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                            placeholder="Email" type="email" />
                                                        <span style="color: red;" id="error-email"></span>
                                                    </label>
                                                    <label class="block">
                                                        <span>{{ __('messages.phone') }}</span>
                                                        <input name="phone" id="phone"
                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                            placeholder="{{ __('messages.phone') }}" type="text" />
                                                        <span style="color: red;" id="error-phone"></span>
                                                    </label>
                                                    <label class="block">
                                                        <span>{{ __('messages.password') }}</span>
                                                        <input name="password" id="paswword"
                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                            placeholder="{{ __('messages.password') }}"
                                                            type="password" />
                                                        <span style="color: red;" id="error-password"></span>
                                                    </label>
                                                    <label class="block">
                                                        <span>Type</span>
                                                        <select name="type" id="type"
                                                            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent">
                                                            <option value="admin">Admin</option>
                                                            <option value="division">Division</option>
                                                        </select>
                                                        <span style="color: red;" id="error-type"></span>
                                                    </label>
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
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="mt-5">
                        <div class="is-scrollbar-hidden min-w-full overflow-x-auto">
                            <table id="example" class="display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="background: #018ea9; color: white;"
                                            class="whitespace-nowrap rounded-l-lg bg-slate-200 px-3 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                            #
                                        </th>
                                        <th style="background: #018ea9; color: white;"
                                            class="whitespace-nowrap bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                            {{ __('messages.name') }}
                                        </th>
                                        <th style="background: #018ea9; color: white;"
                                            class="whitespace-nowrap bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                            E-mail
                                        </th>
                                        <th style="background: #018ea9; color: white;"
                                            class="whitespace-nowrap bg-slate-200 px-3 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                            {{ __('messages.phone') }}
                                        </th>
                                        <th style="background: #018ea9; color: white;"
                                            class="whitespace-nowrap bg-slate-200 px-3 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                            Type
                                        </th>
                                        <th style="background: #018ea9; color: white;"
                                            class="whitespace-nowrap bg-slate-200 px-3 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                            Division
                                        </th>
                                        <th style="background: #018ea9; color: white;"
                                            class="whitespace-nowrap rounded-r-lg bg-slate-200 px-3 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                    @endphp
                                    @foreach ($user as $liste)
                                        @php
                                            $divisions = App\Models\AssoDivisions::leftJoin(
                                                'divisions',
                                                'asso_divisions.division_id',
                                                '=',
                                                'divisions.id',
                                            )
                                                ->where('asso_divisions.user_id', '=', $liste->id)
                                                ->select('divisions.libelle', 'divisions.id')
                                                ->get();
                                        @endphp
                                        <tr>
                                            <td class="whitespace-nowrap rounded-l-lg px-4 py-3 sm:px-5">
                                                {{ $i++ }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                                {{ $liste->name }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                                {{ $liste->email }}
                                            </td>
                                            <td class="whitespace-nowrap rounded-r-lg px-4 py-3 sm:px-5">
                                                {{ $liste->telephone }}
                                            </td>
                                            <td class="whitespace-nowrap rounded-r-lg px-4 py-3 sm:px-5">
                                                {{ $liste->type }}
                                            </td>
                                            <td class="whitespace-nowrap rounded-r-lg px-4 py-3 sm:px-5">
                                                @foreach ($divisions as $divise)
                                                    <div
                                                        class="badge bg-primary text-white shadow-soft shadow-primary/50 dark:bg-accent dark:shadow-accent/50">
                                                        {{ $divise->libelle }}
                                                    </div>
                                                @endforeach
                                            </td>
                                            <td data-column-id="actions" class="gridjs-td">
                                                <span>
                                                    <div class="flex justify-center space-x-2">
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
                                                                            action="{{ route('utilisateurs.update', $liste->id) }}"
                                                                            method="POST" role="form">
                                                                            @csrf
                                                                            @method('PATCH')
                                                                            <div class="px-4 py-4 sm:px-5">
                                                                                <div class="mt-4 space-y-4">
                                                                                    <label class="block">
                                                                                        <span>Division</span>
                                                                                        <select name="division[]"
                                                                                            x-init="$el._tom = new Tom($el)"
                                                                                            class="mt-1.5 w-full" multiple
                                                                                            placeholder="Sélectionne..."
                                                                                            autocomplete="off">
                                                                                            @foreach ($divisions as $divis)
                                                                                                <option selected
                                                                                                    value="{{ $divis->id }}">
                                                                                                    {{ $divis->libelle }}
                                                                                                </option>
                                                                                            @endforeach
                                                                                            @foreach ($division as $item)
                                                                                                <option
                                                                                                    value="{{ $item->id }}">
                                                                                                    {{ $item->libelle }}
                                                                                                </option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                        <span style="color: red;"
                                                                                            id="error-division"></span>
                                                                                    </label>
                                                                                    <label class="block">
                                                                                        <span>{{ __('messages.name') }}</span>
                                                                                        <input name="name" required
                                                                                            id="name"
                                                                                            value="{{ $liste->name }}"
                                                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                                                            placeholder="{{ __('messages.name') }}"
                                                                                            type="text" />
                                                                                        <span style="color: red;"
                                                                                            id="error-name-{{ $liste->id }}"></span>
                                                                                    </label>
                                                                                    <label class="block">
                                                                                        <span>E-mail</span>
                                                                                        <input name="email"
                                                                                            id="email"
                                                                                            value="{{ $liste->email }}"
                                                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                                                            placeholder="Email"
                                                                                            type="email" />
                                                                                        <span style="color: red;"
                                                                                            id="error-email-{{ $liste->id }}"></span>
                                                                                    </label>
                                                                                    <label class="block">
                                                                                        <span>{{ __('messages.phone') }}</span>
                                                                                        <input name="phone"
                                                                                            id="phone"
                                                                                            value="{{ $liste->telephone }}"
                                                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                                                            placeholder="{{ __('messages.phone') }}"
                                                                                            type="text" />
                                                                                        <span style="color: red;"
                                                                                            id="error-phone-{{ $liste->id }}"></span>
                                                                                    </label>
                                                                                    <label class="block">
                                                                                        <span>Type</span>
                                                                                        <select name="type" required
                                                                                            id="type"
                                                                                            class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent">
                                                                                            <option
                                                                                                value="{{ $liste->type }}">
                                                                                                {{ $liste->type }}
                                                                                            </option>
                                                                                            <option value="admin">Admin
                                                                                            </option>
                                                                                            <option value="division">
                                                                                                Division</option>
                                                                                        </select>
                                                                                        <span style="color: red;"
                                                                                            id="error-type-{{ $liste->id }}"></span>
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
                                                                            action="{{ route('utilisateurs.destroy', $liste->id) }}"
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
