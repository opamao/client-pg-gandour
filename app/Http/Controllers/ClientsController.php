<?php

namespace App\Http\Controllers;

use App\Models\Clients;
use App\Models\Divisions;
use App\Models\Stocks;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = Clients::leftJoin('divisions', 'clients.division_id', '=', 'divisions.id')
            ->leftJoin('stocks', 'clients.id', '=', 'stocks.client_id')
            ->select(
                'clients.*',
                'divisions.libelle',
                DB::raw('(SELECT SUM(COALESCE(stocks.quantite_initiale, 0)) FROM stocks WHERE stocks.client_id = clients.id) as sommeQuantiteInitiale')
            )
            ->get();

        $division = Divisions::all();

        return view('clients.clients', compact('clients', 'division'));
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
            'division' => 'required',
            'code' => 'required',
            'name' => 'required',
            'email' => 'required|email|unique:clients,email_client,',
        ];
        $customMessages = [
            'division.required' => "Veuillez sélectionner sa division",
            'code.required' => "Veuillez saisir son code",
            'name' => "Saisissez son nom",
            'email.unique' => "L'adresse email est déjà utilisée. Veuillez essayer une autre!",
        ];
        $request->validate($roles, $customMessages);

        $user = new Clients();
        $user->code_client = $request->code;
        $user->nom_client = $request->name;
        $user->email_client = $request->email;
        $user->division_id = $request->division;
        $user->password_client = Hash::make('1234567890');

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
        $stock = Stocks::where('client_id', '=', $id)->get();
        return view('clients.clients-details', compact('stock'));
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
        $user = Clients::findOrFail($id);

        $roles = [
            'division' => 'required',
            'code' => 'required',
            'name' => 'required',
            'email' => 'required|email|unique:clients,email_client,' . $user->id,
        ];
        $customMessages = [
            'division.required' => "Veuillez sélectionner sa division",
            'code.required' => "Veuillez saisir son code",
            'name' => "Saisissez son nom",
            'email.unique' => "L'adresse email est déjà utilisée. Veuillez essayer une autre!",
        ];
        $request->validate($roles, $customMessages);

        // Mettre à jour les données uniquement si elles ont changé
        $user->code_client = $request->code;
        $user->nom_client = $request->name;
        $user->division_id = $request->division;

        if ($user->email_client !== $request->email) {
            $user->email_client = $request->email;
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
        Clients::findOrFail($id)->delete();

        return back()->with('succes', "La suppression a été effectué");
    }
}
