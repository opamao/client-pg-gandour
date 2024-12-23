<?php

namespace App\Http\Controllers;

use App\Models\AssoDivisions;
use App\Models\Divisions;
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

            $user = User::all();

            $division = Divisions::all();
            return view('utilisateurs.utilisateur', compact('division', 'user'));
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
            'fichier' => 'nullable|mimes:xlsx,xls,csv|max:2048',
        ];
        $customMessages = [
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
                if (empty($row[0]) || empty($row[1])) {
                    continue;
                }

                // Récupère les colonnes du fichier
                $username = $row[0];
                $email = $row[1];
                $telephone = $row[2];
                $password = $row[3];
                $type = $row[4];

                // Création d'utilisateur
                User::create([
                    'name' => $username,
                    'email' => $email,
                    'telephone' => $telephone,
                    'type' => $type,
                    'password' => Hash::make($password),
                ]);

                $successCount++;
            }

            // Retourne les résultats de l'importation
            if ($successCount > 0) {
                return response()->json(['success' => $successCount . " utilisateurs ont été importés avec succès."]);
            }

            return back()->withErrors($errors);
        } else {

            $roles = [
                'division' => 'required|array',
                'name' => 'required',
                'type' => 'required',
                'password' => 'required',
                'phone' => 'nullable|unique:users,telephone',
                'email' => 'required|email|unique:users,email',
            ];
            $customMessages = [
                'division.required' => "Veuillez sélectionner au moins une division.",
                'email.required' => "L'adresse email est obligatoire.",
                'email.unique' => "L'adresse email est déjà utilisée. Veuillez essayer une autre!",
                'phone.unique' => "Le numéro de téléphone est déjà utilisé. Veuillez essayer une autre!",
                'name.required' => "Saisissez son nom",
                'type.required' => "Veuillez sélectionner son type",
                'password.required' => "Saisissez son mot de passe",
            ];
            $request->validate($roles, $customMessages);

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->telephone = $request->phone;
            $user->type = $request->type;
            $user->password = Hash::make($request->password);

            if ($user->save()) {
                foreach ($request->division as $divisionId) {
                    $association = new AssoDivisions();
                    $association->division_id = $divisionId;
                    $association->user_id = $user->id;
                    $association->save();
                }
                return response()->json(['success' => "Vous avez ajouté " . $request->name]);
            } else {
                return response()->json(['errors' => ["Impossible d'ajouter " . $request->name . ". Veuillez réessayer!!"]], 422);
            }
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
            'phone' => 'nullable|unique:users,telephone,' . $user->id,
            'email' => 'nullable|email|unique:users,email,' . $user->id,
        ];

        $customMessages = [
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

            if (!empty($request->division)) {
                // On commence par récupérer toutes les associations existantes pour cet utilisateur
                $existingAssociations = AssoDivisions::where('user_id', $id)->get();

                // Supprimer les associations existantes qui ne correspondent pas à la nouvelle liste
                foreach ($existingAssociations as $association) {
                    if (!in_array($association->division_id, $request->division)) {
                        // Si l'association de division pour l'utilisateur n'est pas dans la nouvelle liste, on la supprime
                        $association->delete();
                    }
                }

                // Ajouter de nouvelles associations si elles n'existent pas déjà
                foreach ($request->division as $newDivisionId) {
                    // Vérifier si l'association existe déjà
                    $existingAssociation = AssoDivisions::where('user_id', $id)
                        ->where('division_id', $newDivisionId)
                        ->first();

                    // Si l'association n'existe pas, on la crée
                    if (!$existingAssociation) {
                        $association = new AssoDivisions();
                        $association->division_id = $newDivisionId;
                        $association->user_id = $id;
                        $association->save();
                    }
                }
            }

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
        AssoDivisions::where('user_id', $id)->delete();

        return back()->with('succes', "La suppression a été effectué");
    }
}
