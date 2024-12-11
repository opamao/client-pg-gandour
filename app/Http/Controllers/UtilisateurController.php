<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UtilisateurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::check()) {

            $division = User::all();
            return view('utilisateurs.utilisateur', compact('division'));

        } else {
            return view('auth.login');
        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $roles = [
            'name' => 'required',
            'type' => 'required',
            'phone' => 'required|unique:users,telephone',
            'email' => 'required|email|unique:users,email',
        ];
        $customMessages = [
            'nom.required' => "Veuillez saisir le nom",
            'type.required' => "Veuillez saisir le prénom",
            'phone.unique' => "Le numéro de téléphone est déjà utilisé. Veuillez essayer un autre!",
            'email.unique' => "L'adresse email est déjà utilisé. Veuillez essayer un autre!",
        ];
        $request->validate($roles, $customMessages);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->telephone = $request->phone;
        $user->type = $request->type;
        $user->password = Hash::make('1234567890');

        if ($user->save()) {
            return back()->with('succes',  "Vous avez ajouter " . $request->name);
        } else {
            return back()->withErrors(["Impossible d'ajouter " . $request->name . ". Veuillez réessayer!!"]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $roles = [
            'name' => 'required',
            'type' => 'required',
            'phone' => 'required|unique:users,telephone,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
        ];

        $customMessages = [
            'name.required' => "Veuillez saisir le nom",
            'type.required' => "Veuillez saisir le type",
            'phone.unique' => "Le numéro de téléphone est déjà utilisé. Veuillez essayer un autre!",
            'email.unique' => "L'adresse email est déjà utilisée. Veuillez essayer une autre!",
        ];

        $request->validate($roles, $customMessages);

        // Mettre à jour les données uniquement si elles ont changé
        $user->name = $request->name;
        $user->type = $request->type;

        if ($user->email !== $request->email) {
            $user->email = $request->email;
        }

        if ($user->telephone !== $request->phone) {
            $user->telephone = $request->phone;
        }

        if ($user->save()) {
            return back()->with('succes', "Les informations de " . $request->name . " ont été mises à jour avec succès.");
        } else {
            return back()->withErrors(["Impossible de mettre à jour les informations de " . $request->name . ". Veuillez réessayer!"]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        User::findOrFail($id)->delete();

        return back()->with('succes', "La suppression a été effectué");
    }
}
