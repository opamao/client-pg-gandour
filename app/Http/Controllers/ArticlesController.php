<?php

namespace App\Http\Controllers;

use App\Models\Articles;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class ArticlesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::check()) {
            // $response = Http::get('http://10.10.32.2:1003/products');

            // $retour = json_decode($response, true);

            // foreach ($retour as $name => $data) {

            //     if ($data['unite'] === 'KAR') {

            //         $control = Articles::where('code_article', $data['code_article'])->first();

            //         if ($control == null) {

            //             $brutesArticle = new Articles();
            //             $brutesArticle->code_article = $data['code_article'] ?? null;
            //             $brutesArticle->unite = $data['unite'] ?? null;
            //             $brutesArticle->cls = $data['cls'] ?? 0;
            //             $brutesArticle->cls2 = $data['cls2'] ?? 0;
            //             $brutesArticle->ref = $data['ref'] ?? null;
            //             $brutesArticle->designation = $data['designation'] ?? null;
            //             $brutesArticle->code_abc = $data['code_abc'] ?? null;
            //             $brutesArticle->designation_abc = $data['designation_abc'] ?? null;
            //             $brutesArticle->PRODH = $data['PRODH'] ?? null;
            //             $brutesArticle->VTEXT = $data['VTEXT'] ?? null;
            //             $brutesArticle->MVGR1 = $data['MVGR1'] ?? null;
            //             $brutesArticle->BEZEI = $data['BEZEI'] ?? null;
            //             $brutesArticle->MVGR2 = $data['MVGR2'] ?? null;
            //             $brutesArticle->BEZE2 = $data['BEZE2'] ?? null;
            //             $brutesArticle->MVGR3 = $data['MVGR3'] ?? null;
            //             $brutesArticle->BEZE3 = $data['BEZE3'] ?? null;
            //             $brutesArticle->MVGR4 = $data['MVGR4'] ?? null;
            //             $brutesArticle->BEZE4 = $data['BEZE4'] ?? null;
            //             $brutesArticle->VMSTA = $data['VMSTA'] ?? null;
            //             $brutesArticle->VMSTD = $data['VMSTD'] ?? null;
            //             $brutesArticle->save();
            //         }
            //     }
            // }

            $nbreArticle = Articles::count();
            $articles = Articles::all();
            return view('articles.articles', compact('nbreArticle', 'articles'));
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
            'fichier' => 'required|mimes:xlsx,xls,csv|max:2048',
        ];
        $customMessages = [
            'fichier.mimes' => __("messages.fileMine"),
            'fichier.max' => __("messages.fileMax"),
        ];
        $request->validate($roles, $customMessages);

        $file = $request->file('fichier');

        // Utiliser Maatwebsite\Excel pour lire le fichier
        $data = \Maatwebsite\Excel\Facades\Excel::toArray([], $file);

        // Vérifie si des données sont disponibles dans le fichier
        if (empty($data) || count($data[0]) === 0) {
            return back()->withErrors([__("messages.fileEmpty")]);
        }

        $rows = $data[0]; // Première feuille du fichier

        $errors = [];
        $successCount = 0;

        foreach ($rows as $index => $row) {
            // Ignore les lignes vides ou mal formatées
            if (empty($row[0]) || empty($row[1]) || empty($row[2])) {
                continue;
            }

            // Récupère les colonnes du fichier
            $nom_article = $row[0];
            $code_article = $row[1];
            $cls_article = $row[2];
            $description_article = $row[3];

            // Création de l'article
            Articles::create([
                'nom_article' => $nom_article,
                'code_article' => $code_article,
                'cls_article' => $cls_article,
                'description_article' => $description_article,
            ]);

            $successCount++;
        }

        // Retourne les résultats de l'importation
        if ($successCount > 0) {
            return back()->with('succes',  $successCount . " articles " . __("messages.fileImport"));
        }

        return back()->withErrors($errors);
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
        $user = Articles::findOrFail($id);

        $roles = [
            'division' => 'required',
            'code' => 'required',
            'name' => 'required',
            'email' => 'required|email|unique:clients,email_client,' . $user->id,
        ];
        $customMessages = [
            'division.required' => __("messages.selectDivision"),
            'username.required' => __("messages.enterUsername"),
            'code.required' => __("messages.enterCode"),
            'name.required' => __("messages.enterName"),
            'email.unique' => __("messages.emailRequired"),
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
            return back()->with('succes', __("messages.update"));
        } else {
            return back()->withErrors([__("messages.impossible")]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Articles::findOrFail($id)->delete();

        return back()->with('succes', __("messages.supprime"));
    }

    public function editPassword(Request $request)
    {
        $roles = [
            'code' => 'required',
            'codenew' => 'required',
            'codeconfirm' => 'required',
        ];
        $customMessages = [
            'code.required' => __("messages.passwordActuel"),
            'codenew.required' => __("messages.codenew"),
            'codeconfirm.required' => __("messages.codeconfirm"),
        ];
        $request->validate($roles, $customMessages);

        if ($request->codenew == $request->codeconfirm) {

            $user = User::where('email', Auth::user()->email)->first();

            if ($user && Hash::check($request->codenew, $user->password)) {

                User::where('id', Auth::user()->id)
                    ->update([
                        'password' => Hash::make($request->codenew),
                    ]);

                return back()->with('succes', __("messages.updatePassword"));
            } else {
                return back()->withErrors([__("messages.updateActuel")]);
            }
        } else {
            return back()->withErrors([__("messages.updateConfirm")]);
        }
    }
}
