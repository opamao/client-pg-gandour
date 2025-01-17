<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Clients;
use App\Models\Divisions;
use App\Models\ImportFichierClient;
use App\Models\Pays;
use App\Models\Stocks;
use GuzzleHttp\Client;
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

        // Récupérer la date d'une semaine et d'un mois en arrière
        $oneWeekAgo = Carbon::now()->subWeek(2); // Date il y a une semaine
        $oneMonthAgo = Carbon::now()->subMonth(); // Date il y a un mois

        if (Auth::check()) {

            if (Auth::user()->type == 'division') {
                $clients = Clients::leftJoin('divisions', 'clients.division_id', '=', 'divisions.id')
                    ->leftJoin('stocks', 'clients.id', '=', 'stocks.client_id')
                    ->leftJoin('pays', 'clients.pays_id', '=', 'pays.id')
                    ->join('asso_divisions', 'clients.division_id', '=', 'asso_divisions.division_id')
                    ->where('asso_divisions.user_id', '=', Auth::user()->id)
                    ->select(
                        'clients.id',
                        'clients.username',
                        'clients.code_client',
                        'clients.email_client',
                        'clients.division_id',
                        'clients.status_client',
                        'divisions.libelle',
                        'clients.pays_id',
                        'clients.created_at',
                        'clients.updated_at',
                        'clients.name_client',
                        'divisions.libelle',
                        'pays.libelle_pays',
                        DB::raw('SUM(COALESCE(stocks.quantite_initiale, 0)) as sommeQuantiteInitiale')
                    )
                    ->groupBy(
                        'clients.id',
                        'clients.username',
                        'clients.code_client',
                        'clients.email_client',
                        'clients.division_id',
                        'divisions.libelle',
                        'clients.pays_id',
                        'clients.name_client',
                        'clients.created_at',
                        'clients.updated_at',
                        'clients.status_client',
                        'divisions.libelle',
                        'pays.libelle_pays',
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
                $pays = Pays::all();

                // Clients qui n'ont pas chargé leur stock depuis une semaine
                $clientsWithoutStockLastWeek = Stocks::join('clients', 'stocks.client_id', '=', 'clients.id')
                    ->join('asso_divisions', 'clients.division_id', '=', 'asso_divisions.division_id')
                    ->where('asso_divisions.user_id', '=', Auth::user()->id)
                    ->where('stocks.updated_at', '<', $oneWeekAgo) // Clients qui n'ont pas chargé leur stock depuis une semaine
                    ->distinct('stocks.client_id') // Pour compter les clients uniques
                    ->count('stocks.client_id'); // Compter le nombre de clients

                // Clients qui n'ont pas chargé leur stock depuis un mois
                $clientsWithoutStockLastMonth = Stocks::join('clients', 'stocks.client_id', '=', 'clients.id')
                    ->join('asso_divisions', 'clients.division_id', '=', 'asso_divisions.division_id')
                    ->where('asso_divisions.user_id', '=', Auth::user()->id)
                    ->where('stocks.updated_at', '<', $oneMonthAgo) // Clients qui n'ont pas chargé leur stock depuis un mois
                    ->distinct('stocks.client_id') // Pour compter les clients uniques
                    ->count('stocks.client_id'); // Compter le nombre de clients

                return view('clients.clients', compact('clients', 'division', 'nbreClient', 'totalStock', 'pays', 'clientsWithoutStockLastWeek', 'clientsWithoutStockLastMonth'));
            } else {
                $clients = Clients::leftJoin('divisions', 'clients.division_id', '=', 'divisions.id')
                    ->leftJoin('stocks', 'clients.id', '=', 'stocks.client_id')
                    ->leftJoin('pays', 'clients.pays_id', '=', 'pays.id')
                    ->select(
                        'clients.id',
                        'clients.username',
                        'clients.code_client',
                        'clients.email_client',
                        'clients.division_id',
                        'clients.pays_id',
                        'clients.name_client',
                        'clients.created_at',
                        'clients.updated_at',
                        'clients.status_client',
                        'divisions.libelle',
                        'pays.libelle_pays',
                        DB::raw('SUM(COALESCE(stocks.quantite_initiale, 0)) as sommeQuantiteInitiale')
                    )
                    ->groupBy(
                        'clients.id',
                        'clients.username',
                        'clients.code_client',
                        'clients.email_client',
                        'clients.division_id',
                        'clients.pays_id',
                        'clients.name_client',
                        'clients.created_at',
                        'clients.updated_at',
                        'clients.status_client',
                        'divisions.libelle',
                        'pays.libelle_pays',
                    )
                    ->get();

                $division = Divisions::all();

                $nbreClient = Clients::count();
                $totalStock = Stocks::sum('quantite_initiale');
                $pays = Pays::orderBy('libelle_pays', 'asc')->get();

                $clientsWithoutStockLastWeek = Stocks::where('updated_at', '<', $oneWeekAgo)->count();
                $clientsWithoutStockLastMonth = Stocks::where('updated_at', '<', $oneMonthAgo)->count();

                return view('clients.clients', compact('clients', 'division', 'nbreClient', 'totalStock', 'pays', 'clientsWithoutStockLastWeek', 'clientsWithoutStockLastMonth'));
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
            'fichier' => 'nullable|mimes:xlsx,xls,csv|max:2048',
        ];
        $customMessages = [
            'fichier.mimes' => __("messages.fileMine"),
            'fichier.max' => __("messages.fileMax"),
        ];
        $validated = $request->validate($roles, $customMessages);

        // Vérifie si un fichier a été uploadé
        if ($request->hasFile('fichier')) {
            $file = $request->file('fichier');

            // Utiliser Maatwebsite\Excel pour lire le fichier
            $data = \Maatwebsite\Excel\Facades\Excel::toArray([], $file);

            // Vérifie si des données sont disponibles dans le fichier
            if (empty($data) || count($data[0]) === 0) {
                return back()->withErrors([__("messages.fileEmpty")]);
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
                $name = $row[3];

                // Création du client
                Clients::create([
                    'username' => $username,
                    'code_client' => $code,
                    'name_client' => $name,
                    'password_client' => Hash::make($password),
                ]);

                $successCount++;
            }

            // Retourne les résultats de l'importation
            if ($successCount > 0) {
                return response()->json(['success' => $successCount . " clients" . __("messages.fileImport")]);
            }

            return back()->withErrors($errors);
        } else {
            // Règles de validation
            $roles = [
                'division' => 'required',
                'password' => 'required',
                'pays' => 'required',
                'username' => 'required',
                'code' => 'required',
                'name' => 'required',
                'email' => 'nullable|email|unique:clients,email_client',
            ];
            $customMessages = [
                'email.unique' => __("messages.emailRequired"),
                'division.required' => __("messages.selectDivision"),
                'pays.required' => __("messages.selectPays"),
                'username.required' => __("messages.enterUsername"),
                'code.required' => __("messages.enterCode"),
                'name.required' => __("messages.enterName"),
                'password.required' => __("messages.enterPassword"),
            ];
            $validated = $request->validate($roles, $customMessages);

            // Traitement manuel (ajout d'un utilisateur unique)
            $user = new Clients();
            $user->username = $request->username;
            $user->code_client = $request->code;
            $user->name_client = $request->name;
            $user->email_client = $request->email;
            $user->pays_id = $request->pays;
            $user->division_id = $request->division;
            $user->status_client = $request->statut;
            $user->password_client = Hash::make($request->password);

            if ($user->save()) {
                return response()->json(['success' => __("messages.fileAdd") . $request->username]);
            } else {
                return response()->json(['errors' => [__("messages.fileImpossible") . $request->username . ". " . __("messages.fileReessaye")]], 422);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $stock = Stocks::where('client_id', '=', $id)
            ->leftJoin('articles', 'stocks.code_stock', '=', 'articles.code_article')
            ->select(
                'stocks.*',
                'articles.designation',
            )
            ->get();
        $client = Clients::leftJoin('divisions', 'clients.division_id', '=', 'divisions.id')
            ->leftJoin('pays', 'clients.pays_id', '=', 'pays.id')
            ->select(
                'clients.*',
                'divisions.libelle',
                'pays.libelle_pays',
            )
            ->where('clients.id', '=', $id)
            ->first();
        return view('clients.clients-details', compact('stock', 'client'));
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

        // Règles de validation
        $roles = [
            'division' => 'required',
            'code' => 'required',
            'nom' => 'required',
            'email' => 'nullable|email|unique:clients,email_client,' . $user->id,
        ];

        // Messages d'erreur personnalisés
        $customMessages = [
            'division.required' => __("messages.selectDivision"),
            'username.required' => __("messages.enterUsername"),
            'code.required' => __("messages.enterCode"),
            'nom.required' => __("messages.enterName"),
            'email.unique' => __("messages.emailRequired"),
        ];

        $request->validate($roles, $customMessages);

        // Traitement du mot de passe et des autres données
        if ($request->password == null) {
            // Mettre à jour les données sans changer le mot de passe
            $user->username = $request->username;
            $user->code_client = $request->code;
            $user->name_client = $request->nom;
            $user->division_id = $request->division;
            $user->status_client = $request->statut;
            $user->pays_id = $request->pays;

            if ($user->email_client !== $request->email) {
                $user->email_client = $request->email;
            }

            if ($user->save()) {
                return back()->with('succes',  __("messages.update"));
            } else {
                return back()->withErrors([__("messages.impossible")]);
            }
        } else {
            // Mettre à jour les données avec le mot de passe
            $user->username = $request->username;
            $user->code_client = $request->code;
            $user->name_client = $request->nom;
            $user->division_id = $request->division;
            $user->pays_id = $request->pays;
            $user->status_client = $request->statut;
            $user->password_client = Hash::make($request->password);

            if ($user->email_client !== $request->email) {
                $user->email_client = $request->email;
            }

            if ($user->save()) {
                return back()->with('succes',  __("messages.update"));
            } else {
                return back()->withErrors([__("messages.impossible")]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Clients::findOrFail($id)->delete();

        return back()->with('succes', __("messages.supprime"));
    }

    public function dateCalcul($date)
    {
        $oneWeekAgo = Carbon::now()->subWeek(2);
        $oneMonthAgo = Carbon::now()->subMonth();

        if ($date == 1) {
            if (Auth::user()->type == 'division') {
                $clientsWithoutStock = Stocks::join('clients', 'stocks.client_id', '=', 'clients.id')
                    ->join('asso_divisions', 'clients.division_id', '=', 'asso_divisions.division_id')
                    ->where('asso_divisions.user_id', '=', Auth::user()->id)
                    ->where('stocks.updated_at', '<', $oneMonthAgo)
                    ->distinct('stocks.client_id')
                    ->get(['stocks.client_id']);

                return view('clients.retard', compact('clientsWithoutStock'));
            } else {
                $clientsWithoutStock = Stocks::join('clients', 'stocks.client_id', '=', 'clients.id')
                    ->where('stocks.updated_at', '<', $oneMonthAgo)
                    ->get(['stocks.client_id']);
                return view('clients.retard', compact('clientsWithoutStock'));
            }
        } else {
            if (Auth::user()->type == 'division') {
                $clientsWithoutStock = Stocks::join('clients', 'stocks.client_id', '=', 'clients.id')
                    ->join('asso_divisions', 'clients.division_id', '=', 'asso_divisions.division_id')
                    ->where('asso_divisions.user_id', '=', Auth::user()->id)
                    ->where('stocks.updated_at', '<', $oneWeekAgo)
                    ->distinct('stocks.client_id')
                    ->get(['stocks.client_id']);

                return view('clients.retard', compact('clientsWithoutStock'));
            } else {
                $clientsWithoutStock = Stocks::join('clients', 'stocks.client_id', '=', 'clients.id')
                    ->where('stocks.updated_at', '<', $oneWeekAgo)
                    ->get(['stocks.client_id']);
                return view('clients.retard', compact('clientsWithoutStock'));
            }
        }
    }
}
