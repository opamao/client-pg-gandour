<?php

namespace App\Console\Commands;

use App\Models\Articles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Met à jour les articles toutes les 20 heures';

    // Exécution de la commande
    public function handle()
    {
        $response = Http::get('http://10.10.2.17:1003/products');
        $retour = json_decode($response, true);

        foreach ($retour as $name => $data) {

            if ($data['unite'] === 'KAR') {

                $control = Articles::where('code_article', $data['code_article'])->first();

                if ($control == null) {

                    $brutesArticle = new Articles();
                    $brutesArticle->code_article = $data['code_article'] ?? null;
                    $brutesArticle->unite = $data['unite'] ?? null;
                    $brutesArticle->cls = $data['cls'] ?? 0;
                    $brutesArticle->cls2 = $data['cls2'] ?? 0;
                    $brutesArticle->ref = $data['ref'] ?? null;
                    $brutesArticle->designation = $data['designation'] ?? null;
                    $brutesArticle->code_abc = $data['code_abc'] ?? null;
                    $brutesArticle->designation_abc = $data['designation_abc'] ?? null;
                    $brutesArticle->PRODH = $data['PRODH'] ?? null;
                    $brutesArticle->VTEXT = $data['VTEXT'] ?? null;
                    $brutesArticle->MVGR1 = $data['MVGR1'] ?? null;
                    $brutesArticle->BEZEI = $data['BEZEI'] ?? null;
                    $brutesArticle->MVGR2 = $data['MVGR2'] ?? null;
                    $brutesArticle->BEZE2 = $data['BEZE2'] ?? null;
                    $brutesArticle->MVGR3 = $data['MVGR3'] ?? null;
                    $brutesArticle->BEZE3 = $data['BEZE3'] ?? null;
                    $brutesArticle->MVGR4 = $data['MVGR4'] ?? null;
                    $brutesArticle->BEZE4 = $data['BEZE4'] ?? null;
                    $brutesArticle->VMSTA = $data['VMSTA'] ?? null;
                    $brutesArticle->VMSTD = $data['VMSTD'] ?? null;
                    $brutesArticle->save();
                }
            }
        }
    }
}
