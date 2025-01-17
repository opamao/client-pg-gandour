<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'code_stock',
        'action',
        'quantite_avant',
        'quantite_apres',
    ];

    protected $primaryKey = 'id';

    protected $table = 'stock_updates';
}
