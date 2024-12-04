<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssoDivisions extends Model
{
    use HasFactory;

    protected $fillable = [
        'division_id',
        'user_id',
    ];

    protected $primaryKey = 'id';

    protected $table = 'asso_divisions';
}
