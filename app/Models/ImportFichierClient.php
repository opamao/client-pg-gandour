<?php

namespace App\Models;

use App\Models\Clients;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class ImportFichierClient extends Model
{
    protected $divisionId;

    // Constructeur pour recevoir la division_id
    public function __construct($divisionId)
    {
        $this->divisionId = $divisionId;
    }

    public function model(array $row)
    {
        return new Clients([
            'code_client' => $row[0],
            'nom_client' => $row[1],
            'email_client' => $row[2],
            'password_client' => Hash::make('1234567890'),
            'division_id' => $this->divisionId,
        ]);
    }
}
