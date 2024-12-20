@extends('layouts.master', [
    'title' => 'Articles',
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
@endpush

@section('content')
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        @include('layouts.status')

        <div class="flex items-center space-x-4 py-5 lg:py-6">
            <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                {{ __('messages.items') }}
            </h2>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:gap-5 lg:grid-cols-2 lg:gap-6">
            <div class="rounded-lg bg-gradient-to-r from-sky-400 to-blue-600 p-1">
                <div class="rounded-lg bg-slate-50 px-4 py-4 dark:bg-navy-900 sm:px-5">
                    <div>
                        <h2 class="text-lg font-medium tracking-wide text-slate-600 line-clamp-1 dark:text-navy-100">
                            {{ __('messages.nbrItem') }}
                        </h2>
                    </div>
                    <div class="pt-2">
                        <p>
                            {{ $nbreArticle }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <br>

        <div class="grid grid-cols-1 gap-4 sm:gap-5 lg:gap-6">
            <div class="card px-4 pb-4 sm:px-5">
                <div class="my-3 flex h-8 items-center justify-between px-4 sm:px-5">
                    <h2 class="font-medium tracking-wide text-slate-700 line-clamp-1 dark:text-navy-100 lg:text-base">

                    </h2>
                    {{-- <div x-data="usePopper({ placement: 'bottom-end', offset: 4 })" @click.outside="isShowPopper &amp;&amp; (isShowPopper = false)"
                        class="inline-flex">
                        <div x-data="{ showModal: false }">
                            <button @click="showModal = true"
                                class="btn relative bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                                Importer article
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
                                                Importation
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
                                        <form action="{{ route('articles.store') }}" method="POST" role="form"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="px-4 py-4 sm:px-5">
                                                <label class="block">
                                                    <span>Fichier</span><br>
                                                    <small><em>Cliquez pour importer le fichier</em></small>
                                                    <input name="fichier"
                                                        class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                        placeholder="Veuillez sélectionner le fichier" type="file"
                                                        accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
                                                </label>
                                                <br><br>
                                                <div class="space-x-2 text-right">
                                                    <button type="submit"
                                                        class="btn min-w-[7rem] rounded-full bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                                                        Enregistrer
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div> --}}
                </div>
            </div>
            <div class="card px-4 pb-4 sm:px-5">
                <div class="mt-5">
                    <div class="is-scrollbar-hidden min-w-full overflow-x-auto">
                        <table id="example" class="is-zebra w-full text-left">
                            <thead>
                                <tr>
                                    <th style="background: #018ea9; color: white;"
                                        class="whitespace-nowrap bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                        Code
                                    </th>
                                    <th style="background: #018ea9; color: white;"
                                        class="whitespace-nowrap bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                        {{ __('messages.unit') }}
                                    </th>
                                    <th style="background: #018ea9; color: white;"
                                        class="whitespace-nowrap bg-slate-200 px-3 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                        CLS
                                    </th>
                                    <th style="background: #018ea9; color: white;"
                                        class="whitespace-nowrap bg-slate-200 px-3 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                        {{ __('messages.designation') }}
                                    </th>
                                    <th style="background: #018ea9; color: white;"
                                        class="whitespace-nowrap rounded-r-lg bg-slate-200 px-3 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($articles as $liste)
                                    <tr>
                                        <td class="whitespace-nowrap rounded-l-lg px-4 py-3 sm:px-5">
                                            {{ $liste->code_article }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                            {{ $liste->unite }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                            {{ $liste->cls }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                            {{ $liste->designation }}
                                        </td>
                                        <td data-column-id="actions" class="gridjs-td">
                                            <span>
                                                <div class="flex justify-center space-x-2">
                                                    <div x-data="{ showModalDelete{{ $liste->id }}: false }">
                                                        <button @click="showModalDelete{{ $liste->id }} = true"
                                                            class="btn size-8 p-0 text-slate-600 hover:bg-info/20 focus:bg-info/20 active:bg-info/25">
                                                            <i class="fa fa-eye"></i>
                                                        </button>
                                                        <template x-teleport="#x-teleport-target">
                                                            <div class="fixed inset-0 z-[100] flex flex-col items-center justify-center overflow-hidden px-4 py-6 sm:px-5"
                                                                x-show="showModalDelete{{ $liste->id }}" role="dialog"
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
                                                                    x-transition:leave-end="opacity-0 scale-95"
                                                                    style="max-width: 80rem;">
                                                                    <div
                                                                        class="flex justify-between rounded-t-lg bg-slate-200 px-4 py-3 dark:bg-navy-800 sm:px-5">
                                                                        <h3
                                                                            class="text-base font-medium text-slate-700 dark:text-navy-100">
                                                                            {{ __('messages.detail') }}
                                                                        </h3>
                                                                        <button
                                                                            @click="showModalDelete{{ $liste->id }} = !showModalDelete{{ $liste->id }}"
                                                                            class="btn -mr-1.5 size-7 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                class="size-4.5" fill="none"
                                                                                viewBox="0 0 24 24" stroke="currentColor"
                                                                                stroke-width="2">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    d="M6 18L18 6M6 6l12 12"></path>
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                    <div class="px-4 py-4 sm:px-5">
                                                                        <div class="mt-4 space-y-4">
                                                                            <div class="flex flex-wrap space-x-4">
                                                                                <div class="w-1/3 bg-blue-500 p-4">
                                                                                    <label class="block">
                                                                                        <span>Code</span>
                                                                                        <h3
                                                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                                                                            {{ $liste->code_article }}
                                                                                            <h4>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="w-1/3 bg-blue-500 p-4">
                                                                                    <label class="block">
                                                                                        <span>{{ __('messages.unit') }}</span>
                                                                                        <h3
                                                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                                                                            {{ $liste->unite }}
                                                                                        </h3>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="w-1/3 bg-blue-500 p-4">
                                                                                    <label class="block">
                                                                                        <span>CLS</span>
                                                                                        <h3
                                                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                                                                            {{ $liste->cls }}
                                                                                        </h3>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="flex flex-wrap space-x-4">
                                                                                <div class="w-1/3 bg-green-500 p-4">
                                                                                    <label class="block">
                                                                                        <span>CLS2</span>
                                                                                        <h3
                                                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                                                                            {{ $liste->cls2 }}
                                                                                        </h3>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="w-1/3 bg-blue-500 p-4">
                                                                                    <label class="block">
                                                                                        <span>{{ __('messages.ref') }}</span>
                                                                                        <h3
                                                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                                                                            {{ $liste->ref }}
                                                                                        </h3>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="w-1/3 bg-green-500 p-4">
                                                                                    <label class="block">
                                                                                        <span>{{ __('messages.designation') }}</span>
                                                                                        <h3
                                                                                            class="form-textarea mt-1.5 w-full resize-none rounded-lg border border-slate-300 bg-transparent p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                                                                            {{ $liste->designation }}
                                                                                        </h3>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="flex flex-wrap space-x-4">
                                                                                <div class="w-1/3 bg-blue-500 p-4">
                                                                                    <label class="block">
                                                                                        <span>Code ABC</span>
                                                                                        <h3
                                                                                            class="form-textarea mt-1.5 w-full resize-none rounded-lg border border-slate-300 bg-transparent p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                                                                            {{ $liste->code_abc }}
                                                                                        </h3>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="w-1/3 bg-green-500 p-4">
                                                                                    <label class="block">
                                                                                        <span>{{ __('messages.designation') }} ABC</span>
                                                                                        <h3
                                                                                            class="form-textarea mt-1.5 w-full resize-none rounded-lg border border-slate-300 bg-transparent p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                                                                            {{ $liste->designation_abc }}
                                                                                        </h3>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="w-1/3 bg-blue-500 p-4">
                                                                                    <label class="block">
                                                                                        <span>PRODH</span>
                                                                                        <h3
                                                                                            class="form-textarea mt-1.5 w-full resize-none rounded-lg border border-slate-300 bg-transparent p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                                                                            {{ $liste->PRODH }}
                                                                                        </h3>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="flex flex-wrap space-x-4">
                                                                                <div class="w-1/3 bg-green-500 p-4">
                                                                                    <label class="block">
                                                                                        <span>VTEXT</span>
                                                                                        <h3
                                                                                            class="form-textarea mt-1.5 w-full resize-none rounded-lg border border-slate-300 bg-transparent p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                                                                            {{ $liste->VTEXT }}
                                                                                        </h3>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="w-1/3 bg-blue-500 p-4">
                                                                                    <label class="block">
                                                                                        <span>MVGR1</span>
                                                                                        <h3
                                                                                            class="form-textarea mt-1.5 w-full resize-none rounded-lg border border-slate-300 bg-transparent p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                                                                            {{ $liste->MVGR1 }}
                                                                                        </h3>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="w-1/3 bg-green-500 p-4">
                                                                                    <label class="block">
                                                                                        <span>BEZEI</span>
                                                                                        <h3
                                                                                            class="form-textarea mt-1.5 w-full resize-none rounded-lg border border-slate-300 bg-transparent p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                                                                            {{ $liste->BEZEI }}
                                                                                        </h3>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="flex flex-wrap space-x-4">
                                                                                <div class="w-1/3 bg-blue-500 p-4">
                                                                                    <label class="block">
                                                                                        <span>MVGR2</span>
                                                                                        <h3
                                                                                            class="form-textarea mt-1.5 w-full resize-none rounded-lg border border-slate-300 bg-transparent p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                                                                            {{ $liste->MVGR2 }}
                                                                                        </h3>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="w-1/3 bg-green-500 p-4">
                                                                                    <label class="block">
                                                                                        <span>BEZE2</span>
                                                                                        <h3
                                                                                            class="form-textarea mt-1.5 w-full resize-none rounded-lg border border-slate-300 bg-transparent p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                                                                            {{ $liste->BEZE2 }}
                                                                                        </h3>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="w-1/3 bg-blue-500 p-4">
                                                                                    <label class="block">
                                                                                        <span>MVGR3</span>
                                                                                        <h3
                                                                                            class="form-textarea mt-1.5 w-full resize-none rounded-lg border border-slate-300 bg-transparent p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                                                                            {{ $liste->MVGR3 }}
                                                                                        </h3>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="flex flex-wrap space-x-4">
                                                                                <div class="w-1/3 bg-green-500 p-4">
                                                                                    <label class="block">
                                                                                        <span>BEZE3</span>
                                                                                        <h3
                                                                                            class="form-textarea mt-1.5 w-full resize-none rounded-lg border border-slate-300 bg-transparent p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                                                                            {{ $liste->BEZE3 }}
                                                                                        </h3>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="w-1/3 bg-blue-500 p-4">
                                                                                    <label class="block">
                                                                                        <span>MVGR4</span>
                                                                                        <h3
                                                                                            class="form-textarea mt-1.5 w-full resize-none rounded-lg border border-slate-300 bg-transparent p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                                                                            {{ $liste->MVGR4 }}
                                                                                        </h3>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="w-1/3 bg-green-500 p-4">
                                                                                    <label class="block">
                                                                                        <span>BEZE4</span>
                                                                                        <h3
                                                                                            class="form-textarea mt-1.5 w-full resize-none rounded-lg border border-slate-300 bg-transparent p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                                                                            {{ $liste->BEZE4 }}
                                                                                        </h3>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="flex flex-wrap space-x-4">
                                                                                <div class="w-1/3 bg-blue-500 p-4">
                                                                                    <label class="block">
                                                                                        <span>VMSTA</span>
                                                                                        <h3
                                                                                            class="form-textarea mt-1.5 w-full resize-none rounded-lg border border-slate-300 bg-transparent p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                                                                            {{ $liste->VMSTA }}
                                                                                        </h3>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="w-1/3 bg-green-500 p-4">
                                                                                    <label class="block">
                                                                                        <span>VMSTD</span>
                                                                                        <h3
                                                                                            class="form-textarea mt-1.5 w-full resize-none rounded-lg border border-slate-300 bg-transparent p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                                                                            {{ $liste->VMSTD }}
                                                                                        </h3>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                    {{-- <div x-data="{ showModal{{ $liste->id }}: false }">
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
                                                                            Modification
                                                                        </h3>
                                                                        <button
                                                                            @click="showModal{{ $liste->id }} = !showModal{{ $liste->id }}"
                                                                            class="btn -mr-1.5 size-7 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                class="size-4.5" fill="none"
                                                                                viewBox="0 0 24 24" stroke="currentColor"
                                                                                stroke-width="2">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    d="M6 18L18 6M6 6l12 12"></path>
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                    <form
                                                                        action="{{ route('articles.update', $liste->id) }}"
                                                                        method="POST" role="form">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <div class="px-4 py-4 sm:px-5">
                                                                            <div class="mt-4 space-y-4">
                                                                                <label class="block">
                                                                                    <span>Code</span>
                                                                                    <input name="name" required
                                                                                        value="{{ $liste->nom_article }}"
                                                                                        class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                                                        placeholder="Saisir son nom"
                                                                                        type="text" />
                                                                                </label>
                                                                                <label class="block">
                                                                                    <span>Unité</span>
                                                                                    <input name="code" required
                                                                                        value="{{ $liste->unite }}"
                                                                                        class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                                                        placeholder="Saisir le code client"
                                                                                        type="text" />
                                                                                </label>
                                                                                <label class="block">
                                                                                    <span>CLS</span>
                                                                                    <input name="cls" required
                                                                                        value="{{ $liste->cls }}"
                                                                                        class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                                                        placeholder="Saisir son prénom"
                                                                                        type="text" />
                                                                                </label>
                                                                                <label class="block">
                                                                                    <span>Désignation</span>
                                                                                    <textarea name="description" rows="4" placeholder="Entrez le texte"
                                                                                        class="form-textarea mt-1.5 w-full resize-none rounded-lg border border-slate-300 bg-transparent p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent">
                                                                                    {{ $liste->designation }}
                                                                                    </textarea>
                                                                                </label>
                                                                                <div class="space-x-2 text-right">
                                                                                    <button type="submit"
                                                                                        class="btn min-w-[7rem] rounded-full bg-info font-medium text-white hover:bg-info-focus focus:bg-info-focus active:bg-info-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                                                                                        Modifier
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
                                                                            Suppression
                                                                        </h3>
                                                                        <button
                                                                            @click="showModalDelete{{ $liste->id }} = !showModalDelete{{ $liste->id }}"
                                                                            class="btn -mr-1.5 size-7 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                class="size-4.5" fill="none"
                                                                                viewBox="0 0 24 24" stroke="currentColor"
                                                                                stroke-width="2">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    d="M6 18L18 6M6 6l12 12"></path>
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                    <form
                                                                        action="{{ route('articles.destroy', $liste->id) }}"
                                                                        method="POST" role="form">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <div class="px-4 py-4 sm:px-5">
                                                                            <div class="mt-4 space-y-4">
                                                                                <label class="block">
                                                                                    Êtes-vous sûre de vouloir supprimer?
                                                                                </label>
                                                                                <div class="space-x-2 text-right">
                                                                                    <button type="submit"
                                                                                        class="btn min-w-[7rem] rounded-full bg-error font-medium text-white hover:bg-error-focus focus:bg-error-focus active:bg-error-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                                                                                        Supprimer
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div> --}}
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
