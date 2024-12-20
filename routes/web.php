<?php

use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\DivisionsController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PaysController;
use App\Http\Controllers\UtilisateurController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('index', [CustomAuthController::class, 'dashboard']);
Route::get('login', [CustomAuthController::class, 'index']);
Route::post('custom-login', [CustomAuthController::class, 'customLogin']);
Route::get('signout', [CustomAuthController::class, 'signOut']);

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->intended('index')->withSuccess('Bon retour');
    }
    return view('auth.login');
});
Route::get('password', function () {
    return view('profile.password');
});

Route::resource('clients', ClientsController::class);
Route::get('retard/{date}', [ClientsController::class, 'dateCalcul']);
Route::resource('utilisateurs', UtilisateurController::class);
Route::resource('divisions', DivisionsController::class);
Route::resource('articles', ArticlesController::class);
Route::resource('pays', PaysController::class);
Route::post('password', [ArticlesController::class, 'editPassword']);
Route::post('language-switch', [LanguageController::class, 'languageSwitch'])->name('language.switch');
