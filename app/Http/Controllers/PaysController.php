<?php

namespace App\Http\Controllers;

use App\Models\Pays;
use Illuminate\Http\Request;

class PaysController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pays = Pays::all();
        return view('pays.pays', compact('pays'));
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
        ];
        $customMessages = [
            'libelle.required' => "Veuillez saisir le libelle de le pays",
        ];
        $request->validate($roles, $customMessages);

        $pays = new Pays();
        $pays->libelle_pays = $request->libelle;
        if ($pays->save()) {
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
        $roles = [
            'libelle' => 'required',
        ];

        $customMessages = [
            'libelle.required' => "Veuillez saisir le libelle de le Pays",
        ];

        $request->validate($roles, $customMessages);

        $pays = Pays::find($id);
        if (!$pays) {
            return back()->withErrors(["Impossible de trouver le Pays"]);
        }

        $pays->libelle_pays = $request->libelle;
        $pays->save();

        return back()->with('succes', "Vous avez modifié le Pays avec succès.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Pays::findOrFail($id)->delete();

        return back()->with('succes', "La suppression a été effectué");
    }
}
