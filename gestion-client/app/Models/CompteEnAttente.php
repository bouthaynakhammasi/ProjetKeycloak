<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompteEnAttente extends Model
{
    use HasFactory;

    protected $table = 'comptes_en_attente';

    protected $fillable = [
        'keycloak_id',
        'nom',
        'prenom',
        'email',
        'statut',
    ];

    const STATUT_EN_ATTENTE = 'en_attente';
    const STATUT_TRAITE = 'traite';
}
?>
