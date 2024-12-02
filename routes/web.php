<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('index', function () {
    return view('dashboard.dashboard');
});
Route::get('clients', function () {
    return view('clients.clients');
});
Route::get('divisions', function () {
    return view('divisions.divisions');
});
