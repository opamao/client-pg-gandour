@extends('layouts.master', [
    'title' => 'Stocks retards',
])

@push('haut')
    <link href="{{ asset('assets/table/css') }}/dataTables.tailwindcss.css" />
@endpush

@push('bas')
    <script src="{{ asset('assets/table/js') }}/jquery-3.7.1.js"></script>
    {{-- <link href="{{ asset('assets/table/css') }}/tailwindcss.css" /> --}}
    <script src="{{ asset('assets/table/js') }}/dataTables.js"></script>
    <script src="{{ asset('assets/table/js') }}/dataTables.tailwindcss.js"></script>
@endpush

@section('content')
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        @include('layouts.status')

        <div class="flex items-center space-x-4 py-5 lg:py-6">
            <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                Client en retard de stock
            </h2>
        </div>
        <br>
        <br>
        <div class="grid grid-cols-1 gap-4 sm:gap-5 lg:gap-6">
            <div class="card px-4 pb-4 sm:px-5">
                <div class="mt-5">
                    <div class="is-scrollbar-hidden min-w-full overflow-x-auto">
                        <table id="example" class="is-zebra w-full text-left">
                            <thead>
                                <tr>
                                    <th style="background: #018ea9; color: white;"
                                        class="whitespace-nowrap bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                        Username
                                    </th>
                                    <th style="background: #018ea9; color: white;"
                                        class="whitespace-nowrap bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                        Nom
                                    </th>
                                    <th style="background: #018ea9; color: white;"
                                        class="whitespace-nowrap bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                        Code
                                    </th>
                                    <th style="background: #018ea9; color: white;"
                                        class="whitespace-nowrap bg-slate-200 px-3 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                        Email
                                    </th>
                                    <th style="background: #018ea9; color: white;"
                                        class="whitespace-nowrap bg-slate-200 px-3 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                        Statut
                                    </th>
                                    <th style="background: #018ea9; color: white;"
                                        class="whitespace-nowrap bg-slate-200 px-3 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                        Date create
                                    </th>
                                    <th style="background: #018ea9; color: white;"
                                        class="whitespace-nowrap bg-slate-200 px-3 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                        Date update
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clientsWithoutStock as $liste)
                                    <tr>
                                        <td class="whitespace-nowrap rounded-l-lg px-4 py-3 sm:px-5">
                                            {{ $liste->username }}
                                        </td>
                                        <td class="whitespace-nowrap rounded-l-lg px-4 py-3 sm:px-5">
                                            {{ $liste->name_client }}
                                        </td>
                                        <td class="whitespace-nowrap rounded-l-lg px-4 py-3 sm:px-5">
                                            {{ $liste->code_client }}
                                        </td>
                                        <td class="whitespace-nowrap rounded-l-lg px-4 py-3 sm:px-5">
                                            {{ $liste->email_client }}
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
                                                    Désactive
                                                </div>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap rounded-r-lg px-4 py-3 sm:px-5">
                                            {{ $liste->created_at }}
                                        </td>
                                        <td class="whitespace-nowrap rounded-r-lg px-4 py-3 sm:px-5">
                                            {{ $liste->updated_at }}
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
