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
            'fichier.mimes' => __("messages.fileMine"),
            'fichier.max' => __("messages.fileMax"),
        ];
        $request->validate($roles, $customMessages);

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
                return response()->json(['success' => $successCount . " utilisateurs" . __("messages.fileImport")]);
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
                'phone.unique' => __("messages.phoneUser"),
                'email.required' => __("messages.emailRequired"),
                'email.unique' => __("messages.emailRequired"),
                'division.required' => __("messages.divisionMulti"),
                'name.required' => __("messages.enterName"),
                'password.required' => __("messages.enterPassword"),
                'type.required' => __("messages.type"),
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
                return response()->json(['success' => __("messages.fileAdd") . $request->name]);
            } else {
                return response()->json(['errors' => [__("messages.fileImpossible") . $request->name . ". " . __("messages.fileReessaye")]], 422);
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
            'phone.unique' => __("messages.phoneUser"),
            'email.unique' => __("messages.emailRequired"),
        ];

        $request->validate($roles, $customMessages);

        if ($request->password == null) {
            // Mettre à jour les données uniquement si elles ont changé
            $user->name = $request->name;
            $user->type = $request->type;
            $user->status = $request->statut;

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

                return back()->with('succes', __("messages.update"));
            } else {
                return back()->withErrors([__("messages.impossible")]);
            }
        } else {
            // Mettre à jour les données uniquement si elles ont changé
            $user->name = $request->name;
            $user->type = $request->type;
            $user->status = $request->statut;
            $user->password = Hash::make($request->password);

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

                return back()->with('succes', __("messages.update"));
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
        AssoDivisions::where('user_id', '=', $id)->delete();
        User::findOrFail($id)->delete();

        return back()->with('succes', __("messages.supprime"));
    }
}
