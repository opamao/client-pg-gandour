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
            'email' => 'required',
            'password' => 'required',
        ];
        $customMessages = [
            'email.required' => __("messages.adresseEmail"),
            'password.required' => __("messages.adressePassword"),
        ];
        $request->validate($roles, $customMessages);

        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {

            if ($user->status == 0) {
                return back()->withInput()->withErrors(["Votre compte n'est pas autoriser a utiliser la plateforme."]);
            } else {
                // Lorque les paramètres sont valides, garde les informations dans la session
                Auth::login($user);

                return redirect()->intended('index')->withSuccess('Bon retour');
            }
        } else {
            // Les identifiants ne sont pas valides
            return back()->withInput()->withErrors([__("messages.emailPassword")]);
        }
    }

    public function dashboard()
    {
        if (Auth::check()) {

            $nbreClient = Clients::count();
            $division = Divisions::count();
            $article = Articles::count();
            $totalStock = Stocks::sum('quantite_initiale');

            return view('dashboard.dashboard', compact('nbreClient', 'totalStock', 'division', 'article'));
        } else {
            return view('auth.login');
        }
    }

    public function signOut()
    {
        Session::flush();
        Auth::logout();

        return Redirect('/');
    }
}
