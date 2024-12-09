<?php

namespace App\Http\Controllers;

use App\Models\Clients;
use App\Models\Divisions;
use App\Models\ImportFichierClient;
use App\Models\Stocks;
use Illuminate\Http\Request;
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
        $clients = Clients::leftJoin('divisions', 'clients.division_id', '=', 'divisions.id')
            ->leftJoin('stocks', 'clients.id', '=', 'stocks.client_id')
            ->select(
                'clients.id',
                'clients.code_client',
                'clients.nom_client',
                'clients.email_client',
                'clients.division_id',
                'divisions.libelle',
                DB::raw('SUM(COALESCE(stocks.quantite_initiale, 0)) as sommeQuantiteInitiale')
            )
            ->groupBy(
                'clients.id',
                'clients.code_client',
                'clients.nom_client',
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
            'division' => 'required',
            'code' => 'nullable',
            'name' => 'nullable',
            'email' => 'nullable|email|unique:clients,email_client',
            'fichier' => 'nullable|mimes:xlsx,xls,csv|max:2048',
        ];
        $customMessages = [
            'division.required' => "Veuillez sélectionner sa division",
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
            $division_id = $request->division; // Récupération de l'input division

            $errors = [];
            $successCount = 0;

            foreach ($rows as $index => $row) {
                // Ignore les lignes vides ou mal formatées
                if (empty($row[0]) || empty($row[1]) || empty($row[2])) {
                    continue;
                }

                // Récupère les colonnes du fichier
                $code_client = $row[0];
                $nom_client = $row[1];
                $email_client = $row[2];

                // Validation pour chaque ligne
                $validator = Validator::make(
                    [
                        'email_client' => $email_client,
                    ],
                    [
                        'email_client' => 'required|email|unique:clients,email_client',
                    ],
                    [
                        'email_client.unique' => "L'email {$email_client} existe déjà.",
                    ]
                );

                if ($validator->fails()) {
                    $errors[] = "Ligne {" . $index + 1 . "} : " . implode(', ', $validator->errors()->all());
                    continue;
                }

                // Création du client
                Clients::create([
                    'code_client' => $code_client,
                    'nom_client' => $nom_client,
                    'email_client' => $email_client,
                    'password_client' => Hash::make('1234567890'),
                    'division_id' => $division_id,
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
            $user->code_client = $request->code;
            $user->nom_client = $request->name;
            $user->email_client = $request->email;
            $user->division_id = $request->division;
            $user->password_client = Hash::make('1234567890');

            if ($user->save()) {
                return back()->with('succes', "Vous avez ajouté " . $request->name);
            } else {
                return back()->withErrors(["Impossible d'ajouter " . $request->name . ". Veuillez réessayer!!"]);
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
