<?php

namespace App\Exports;

use App\Models\Articles;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StocksExport implements FromCollection, WithHeadings, WithMapping
{
    protected $id;

    // Constructeur pour accepter les paramètres
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        // Effectuer un left join entre articles et stocks
        return Articles::leftJoin('stocks', function ($join) {
            $join->on('articles.code_article', '=', 'stocks.code_stock')
                ->where('stocks.client_id', '=', $this->id);
        })
            ->select('articles.code_article', 'articles.designation', 'stocks.quantite_initiale')
            ->get();
    }

    /**
     * Définir les en-têtes des colonnes pour l'exportation
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'code_article',
            'designation',
            'quantite_initiale',
        ];
    }

    /**
     * Mapper les données de chaque ligne
     *
     * @param  \App\Models\Article  $article
     * @return array
     */
    public function map($article): array
    {
        // Remplacer la quantité par 0 si elle est null
        $stock = $article->quantite_initiale ?? 0;

        return [
            $article->code_article,
            $article->designation,
            $stock,
        ];
    }
}
