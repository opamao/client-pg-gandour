<?php

use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\DivisionsController;
use App\Http\Controllers\UtilisateurController;
use Illuminate\Support\Facades\Route;

Route::get('index', [CustomAuthController::class, 'dashboard']);
Route::get('login', [CustomAuthController::class, 'index']);
Route::post('custom-login', [CustomAuthController::class, 'customLogin']);
Route::get('signout', [CustomAuthController::class, 'signOut']);

Route::get('/', function () {
    if (session()->has('id')) {
        return redirect()->intended('index')->withSuccess('Bon retour');
    }
    return view('auth.login');
});

Route::resource('clients', ClientsController::class);
Route::resource('users', UtilisateurController::class);
Route::resource('divisions', DivisionsController::class);
Route::resource('articles', ArticlesController::class);
