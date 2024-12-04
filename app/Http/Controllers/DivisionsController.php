<?php

namespace App\Http\Controllers;

use App\Models\AssoDivisions;
use App\Models\Divisions;
use App\Models\User;
use Illuminate\Http\Request;

class DivisionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $division = Divisions::all();
        $membre = User::all();
        return view('divisions.division', compact('division', 'membre'));
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
            AssoDivisions::where('division_id', $id)->delete();

            foreach ($request->membre as $newUserId) {
                $association = new AssoDivisions();
                $association->division_id = $division->id;
                $association->user_id = $newUserId;
                $association->save();
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
