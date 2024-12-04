<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisions extends Model
{
    use HasFactory;

    protected $fillable = [
        'libelle',
    ];

    protected $primaryKey = 'id';

    protected $table = 'divisions';
}
