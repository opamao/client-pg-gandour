<?php

namespace App\Http\Controllers;

use App\Models\AssoDivisions;
use App\Models\Divisions;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DivisionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::check()) {

            $division = Divisions::all();
            $membre = User::where('type', '=', 'division')->get();
            return view('divisions.division', compact('division', 'membre'));
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
            'libelle' => 'required',
            'membre' => 'required|array',
        ];
        $customMessages = [
            'libelle.required' => "Veuillez saisir le libelle de la division",
            'membre.required' => "Veuillez sélectionner au moins un membre",
        ];
        $request->validate($roles, $customMessages);

        $division = new Divisions();
        $division->libelle = $request->libelle;

        if ($division->save()) {
            foreach ($request->membre as $userId) {
                $association = new AssoDivisions();
                $association->division_id = $division->id;
                $association->user_id = $userId;
                $association->save();
            }
            return back()->with('succes',  "Vous avez ajouter " . $request->libelle);
        } else {
            return back()->withErrors(["Impossible d'ajouter " . $request->libelle . ". Veuillez réessayer!!"]);
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
        // Validation des données
        $roles = [
            'libelle' => 'required',
            'membre' => '',
        ];

        $customMessages = [
            'libelle.required' => "Veuillez saisir le libelle de la division",
        ];

        $request->validate($roles, $customMessages);

        $division = Divisions::find($id);
        if (!$division) {
            return back()->withErrors(["Impossible de trouver la division"]);
        }

        $division->libelle = $request->libelle;
        $division->save();

        if (!empty($request->membre)) {
            // On commence par récupérer toutes les associations existantes pour cet utilisateur
            $existingAssociations = AssoDivisions::where('division_id', $id)->get();

            // Supprimer les associations existantes qui ne correspondent pas à la nouvelle liste
            foreach ($existingAssociations as $association) {
                if (!in_array($association->user_id, $request->membre)) {
                    // Si l'association de division pour l'utilisateur n'est pas dans la nouvelle liste, on la supprime
                    $association->delete();
                }
            }

            // Ajouter de nouvelles associations si elles n'existent pas déjà
            foreach ($request->membre as $newMembreId) {
                // Vérifier si l'association existe déjà
                $existingAssociation = AssoDivisions::where('division_id', $id)
                    ->where('user_id', $newMembreId)
                    ->first();

                // Si l'association n'existe pas, on la crée
                if (!$existingAssociation) {
                    $association = new AssoDivisions();
                    $association->division_id = $id;
                    $association->user_id = $newMembreId;
                    $association->save();
                }
            }
        }

        return back()->with('succes', "Vous avez modifié la division avec succès.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Divisions::findOrFail($id)->delete();

        return back()->with('succes', "La suppression a été effectué");
    }
}
