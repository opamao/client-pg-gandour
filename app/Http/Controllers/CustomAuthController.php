<?php

namespace App\Http\Controllers;

use App\Models\Articles;
use App\Models\Clients;
use App\Models\Divisions;
use App\Models\Stocks;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class CustomAuthController extends Controller
{
    public function index()
    {
        return view("auth.login");
    }

    public function customLogin(Request $request)
    {
        $roles = [
            'email' => 'required|email',
            'password' => 'required',
        ];
        $customMessages = [
            'email.required|email' => "Veuillez saisir votre adresse email",
            'password.required' => "Veuillez saisir cotre mot de passe",
        ];
        $request->validate($roles, $customMessages);

        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            // Lorque les paramètres sont valides, garde les informations dans la session
            Auth::login($user);

            return redirect()->intended('index')->withSuccess('Bon retour');

            // if (Auth::user()->type_user == 'responsable') {

            //     return redirect()->intended('comme')->withSuccess('Bon retour');
            // } else if (Auth::user()->type_user == 'directeur') {

            //     return redirect()->intended('dashboard')->withSuccess('Bon retour');
            // }else if (Auth::user()->type_user == 'super') {

            //     return redirect()->intended('entreprise')->withSuccess('Bon retour');
            // }

            // return redirect('index')->withErrors(["Vous n'êtes pas autoriser"]);
        } else {
            // Les identifiants ne sont pas valides
            return back()->withInput()->withErrors(['E-mail ou mot de passe incorrect']);
        }
    }

    public function dashboard()
    {
        $nbreClient = Clients::count();
        $division = Divisions::count();
        $article = Articles::count();
        $totalStock = Stocks::sum('quantite_initiale');

        return view('dashboard.dashboard', compact('nbreClient', 'totalStock', 'division', 'article'));
    }

    public function signOut()
    {
        Session::flush();
        Auth::logout();

        return Redirect('/');
    }
}
