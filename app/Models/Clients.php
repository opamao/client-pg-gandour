<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clients extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'code_client',
        'name_client',
        'email_client',
        'logo_client',
        'pays_id',
        'status_client',
        'last_sync_attempt',
        'password_client',
        'division_id',
    ];

    protected $primaryKey = 'id';

    protected $table = 'clients';
}
