<?php

use App\Http\Controllers\ClientsController;
use App\Http\Controllers\DivisionsController;
use App\Http\Controllers\UtilisateurController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('index', function () {
    return view('dashboard.dashboard');
});

Route::resource('clients', ClientsController::class);
Route::resource('users', UtilisateurController::class);
Route::resource('divisions', DivisionsController::class);
