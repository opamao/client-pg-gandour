<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clients extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_client',
        'precode_client',
        'name_client',
        'email_client',
        'logo_client',
        'address_client',
        'status_client',
        'last_sync_attempt',
        'password_client',
        'division_id',
    ];

    protected $primaryKey = 'id';

    protected $table = 'clients';
}
