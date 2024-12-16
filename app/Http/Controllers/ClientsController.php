<?php

namespace App\Http\Controllers;

use App\Models\Clients;
use App\Models\Divisions;
use App\Models\ImportFichierClient;
use App\Models\Stocks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::check()) {

            if (Auth::user()->type == 'division') {
                $clients = Clients::leftJoin('divisions', 'clients.division_id', '=', 'divisions.id')
                    ->leftJoin('stocks', 'clients.id', '=', 'stocks.client_id')
                    ->join('asso_divisions', 'clients.division_id', '=', 'asso_divisions.division_id')
                    ->where('asso_divisions.user_id', '=', Auth::user()->id)
                    ->select(
                        'clients.id',
                        'clients.username',
                        'clients.code_client',
                        'clients.precode_client',
                        'clients.email_client',
                        'clients.division_id',
                        'divisions.libelle',
                        DB::raw('SUM(COALESCE(stocks.quantite_initiale, 0)) as sommeQuantiteInitiale')
                    )
                    ->groupBy(
                        'clients.id',
                        'clients.username',
                        'clients.code_client',
                        'clients.precode_client',
                        'clients.email_client',
                        'clients.division_id',
                        'divisions.libelle'
                    )
                    ->get();

                $division = Divisions::all();

                $nbreClient = Clients::join('asso_divisions', 'clients.division_id', '=', 'asso_divisions.division_id')
                    ->where('asso_divisions.user_id', '=', Auth::user()->id)
                    ->count();
                $totalStock = Stocks::join('clients', 'stocks.client_id', '=', 'clients.id')
                    ->join('asso_divisions', 'clients.division_id', '=', 'asso_divisions.division_id')
                    ->where('asso_divisions.user_id', '=', Auth::user()->id)
                    ->sum('stocks.quantite_initiale');

                return view('clients.clients', compact('clients', 'division', 'nbreClient', 'totalStock'));
            } else {
                $clients = Clients::leftJoin('divisions', 'clients.division_id', '=', 'divisions.id')
                    ->leftJoin('stocks', 'clients.id', '=', 'stocks.client_id')
                    ->select(
                        'clients.id',
                        'clients.username',
                        'clients.code_client',
                        'clients.precode_client',
                        'clients.email_client',
                        'clients.division_id',
                        'divisions.libelle',
                        DB::raw('SUM(COALESCE(stocks.quantite_initiale, 0)) as sommeQuantiteInitiale')
                    )
                    ->groupBy(
                        'clients.id',
                        'clients.username',
                        'clients.code_client',
                        'clients.precode_client',
                        'clients.email_client',
                        'clients.division_id',
                        'divisions.libelle'
                    )
                    ->get();

                $division = Divisions::all();

                $nbreClient = Clients::count();
                $totalStock = Stocks::sum('quantite_initiale');

                return view('clients.clients', compact('clients', 'division', 'nbreClient', 'totalStock'));
            }
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
        // Règles de validation
        $roles = [
            'division' => 'nullable',
            'code' => 'nullable',
            'name' => 'nullable',
            'email' => 'nullable|email|unique:clients,email_client',
            'fichier' => 'nullable|mimes:xlsx,xls,csv|max:2048',
        ];
        $customMessages = [
            'email.unique' => "L'adresse email est déjà utilisée. Veuillez essayer une autre!",
            'fichier.mimes' => "Le fichier doit être un fichier de type : xlsx, xls, ou csv.",
            'fichier.max' => "La taille du fichier ne doit pas dépasser 2 Mo.",
        ];
        $request->validate($roles, $customMessages);

        // Vérifie si un fichier a été uploadé
        if ($request->hasFile('fichier')) {
            $file = $request->file('fichier');

            // Utiliser Maatwebsite\Excel pour lire le fichier
            $data = \Maatwebsite\Excel\Facades\Excel::toArray([], $file);

            // Vérifie si des données sont disponibles dans le fichier
            if (empty($data) || count($data[0]) === 0) {
                return back()->withErrors(["Le fichier est vide ou mal formaté."]);
            }

            $rows = $data[0]; // Première feuille du fichier

            array_shift($rows);

            $errors = [];
            $successCount = 0;

            foreach ($rows as $index => $row) {
                // Ignore les lignes vides ou mal formatées
                if (empty($row[0])) {
                    continue;
                }

                // Récupère les colonnes du fichier
                $username = $row[0];
                $password = $row[1];
                $code = $row[2];
                $precode = $row[3];
                $name = $row[4];
                $adresse = $row[5];
                $status = $row[6];
                $division = $row[7];

                // Création du client
                Clients::create([
                    'username' => $username,
                    'code_client' => $code,
                    'precode_client' => $precode,
                    'name_client' => $name,
                    'address_client' => $adresse,
                    'status_client' => $status,
                    'password_client' => Hash::make($password),
                    'division_id' => $division,
                ]);

                $successCount++;
            }

            // Retourne les résultats de l'importation
            if ($successCount > 0) {
                return back()->with('succes',  $successCount . " clients ont été importés avec succès.");
            }

            return back()->withErrors($errors);
        } else {
            // Traitement manuel (ajout d'un utilisateur unique)
            $user = new Clients();
            $user->username = $request->username;
            $user->code_client = $request->code;
            $user->precode_client = $request->precode;
            $user->nom_client = $request->name;
            $user->email_client = $request->email;
            $user->address_client = $request->pays;
            $user->division_id = $request->division;
            $user->password_client = Hash::make($request->password);

            if ($user->save()) {
                return back()->with('succes', "Vous avez ajouté " . $request->username);
            } else {
                return back()->withErrors(["Impossible d'ajouter " . $request->username . ". Veuillez réessayer!!"]);
            }
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
