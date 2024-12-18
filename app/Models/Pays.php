<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pays extends Model
{
    use HasFactory;

    protected $fillable = [
        'libelle_pays',
     ];

     protected $primaryKey = 'id';

     protected $table = 'pays';
}
