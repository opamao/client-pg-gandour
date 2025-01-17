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
@endpush

@section('content')
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        @include('layouts.status')

        <div class="flex items-center space-x-4 py-5 lg:py-6">
            <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                {{ __('messages.detail') }} {{ __('messages.client') }}
            </h2>
        </div>
        <div class="card col-span-12 lg:col-span-12">
            <div class="mt-4 grid grid-cols-2 gap-3 px-4 sm:mt-5 sm:grid-cols-4 sm:gap-5 sm:px-5 lg:mt-6">
                <div class="rounded-lg bg-slate-100 p-4 dark:bg-navy-600">
                    <div class="flex justify-between space-x-1">
                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                            {{ $client->username }}
                        </p>
                    </div>
                    <p class="mt-1 text-xs+">{{ __('messages.username') }}</p>
                </div>
                <div class="rounded-lg bg-slate-100 p-4 dark:bg-navy-600">
                    <div class="flex justify-between space-x-1">
                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                            {{ $client->code_client }}
                        </p>
                    </div>
                    <p class="mt-1 text-xs+">Code</p>
                </div>
                <div class="rounded-lg bg-slate-100 p-4 dark:bg-navy-600">
                    <div class="flex justify-between space-x-1">
                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                            {{ $client->name_client }}
                        </p>
                    </div>
                    <p class="mt-1 text-xs+">{{ __('messages.name') }}</p>
                </div>
                <div class="rounded-lg bg-slate-100 p-4 dark:bg-navy-600">
                    <div class="flex justify-between space-x-1">
                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                            {{ $client->email_client }}
                        </p>
                    </div>
                    <p class="mt-1 text-xs+">Email</p>
                </div>
                <div class="rounded-lg bg-slate-100 p-4 dark:bg-navy-600">
                    <div class="flex justify-between space-x-1">
                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                            {{ $client->libelle_pays  }}
                        </p>
                    </div>
                    <p class="mt-1 text-xs+">{{ __('messages.pays') }}</p>
                </div>
                <div class="rounded-lg bg-slate-100 p-4 dark:bg-navy-600">
                    <div class="flex justify-between space-x-1">
                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                            {{ $client->libelle  }}
                        </p>
                    </div>
                    <p class="mt-1 text-xs+">Division</p>
                </div>
                <div class="rounded-lg bg-slate-100 p-4 dark:bg-navy-600">
                    <div class="flex justify-between space-x-1">
                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                            {{ $client->created_at }}
                        </p>
                    </div>
                    <p class="mt-1 text-xs+">{{ __('messages.dateCreate') }}</p>
                </div>
                <div class="rounded-lg bg-slate-100 p-4 dark:bg-navy-600">
                    <div class="flex justify-between space-x-1">
                        <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                            {{ $client->updated_at }}
                        </p>
                    </div>
                    <p class="mt-1 text-xs+">{{ __('messages.dateUpdate') }}</p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-4 sm:gap-5 lg:gap-6">
            <div class="card px-4 pb-4 sm:px-5">
                <br>
                <div class="my-3 flex h-8 items-center justify-between px-4 sm:px-5">
                    <h2 class="font-medium tracking-wide text-slate-700 line-clamp-1 dark:text-navy-100 lg:text-base">
                    </h2>
                    <div x-data="usePopper({ placement: 'bottom-end', offset: 4 })" @click.outside="isShowPopper &amp;&amp; (isShowPopper = false)"
                        class="inline-flex">
                        <div x-data="{ showModal: false }">
                            <a href="{{ url('export', [$client->id, $client->code_client]) }}"
                                class="btn relative bg-secondary font-medium text-white hover:bg-secondary-focus focus:bg-secondary-focus active:bg-secondary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                                {{ __('messages.export') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="mt-5">
                        <div class="is-scrollbar-hidden min-w-full overflow-x-auto">
                            <table id="example" class="display">
                                <thead>
                                    <tr>
                                        <th style="background: #018ea9; color: white;"
                                            class="whitespace-nowrap bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                            Code
                                        </th>
                                        <th style="background: #018ea9; color: white;"
                                            class="whitespace-nowrap bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                            {{ __('messages.quantite') }}
                                        </th>
                                        <th style="background: #018ea9; color: white;"
                                            class="whitespace-nowrap bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                            Designation
                                        </th>
                                        <th style="background: #018ea9; color: white;"
                                            class="whitespace-nowrap bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                            Date
                                        </th>
                                        <th style="background: #018ea9; color: white;"
                                            class="whitespace-nowrap rounded-r-lg bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">

                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stock as $liste)
                                        <tr>
                                            <td class="whitespace-nowrap rounded-l-lg px-4 py-3 sm:px-5">
                                                {{ $liste->code_stock }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                                {{ $liste->quantite_initiale }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                                {{ $liste->designation }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                                {{ $liste->created_at }}
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
                                                                        x-transition:leave-end="opacity-0 scale-95"
                                                                        style="max-width: 60rem;">
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
                                                                                    viewBox="0 0 24 24"
                                                                                    stroke="currentColor" stroke-width="2">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        d="M6 18L18 6M6 6l12 12"></path>
                                                                                </svg>
                                                                            </button>
                                                                        </div>
                                                                        <div class="px-4 py-4 sm:px-5">
                                                                            @php
                                                                                $stockUp = \App\Models\StockUpdate::where(
                                                                                    'code_stock',
                                                                                    '=',
                                                                                    $liste->code_stock,
                                                                                )->get();
                                                                            @endphp
                                                                            <div class="mt-4 space-y-4">
                                                                                <table class="is-zebra w-full text-left">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th style="background: #018ea9; color: white;"
                                                                                                class="whitespace-nowrap bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                                                                                Action</th>
                                                                                            <th style="background: #018ea9; color: white;"
                                                                                                class="whitespace-nowrap bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                                                                                Quantité avant</th>
                                                                                            <th style="background: #018ea9; color: white;"
                                                                                                class="whitespace-nowrap bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                                                                                Quantité après</th>
                                                                                            <th style="background: #018ea9; color: white;"
                                                                                                class="whitespace-nowrap bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                                                                                Created</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        @foreach ($stockUp as $itemCli)
                                                                                            <tr>
                                                                                                <td>{{ $itemCli->action }}
                                                                                                </td>
                                                                                                <td>{{ $itemCli->quantite_avant }}
                                                                                                </td>
                                                                                                <td>{{ $itemCli->quantite_apres }}
                                                                                                </td>
                                                                                                <td>{{ $itemCli->created_at }}
                                                                                                </td>
                                                                                            </tr>
                                                                                        @endforeach
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
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
