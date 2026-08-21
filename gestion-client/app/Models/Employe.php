<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employe extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Les champs assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'keycloak_id',
        'nom',
        'prenom',
        'email',
        'telephone',
        'localisation',
        'bio',
        'notifications_actives',
        'coordonnees_bancaires',
        'poste',
        'departement',
        'date_embauche',
        'statut',
        'photo',
        'conges_payes',
        'conges_maladie',
        'heures_recuperation',
    ];

    /**
     * Les champs qui doivent être convertis en types natifs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_embauche' => 'date',
        'notifications_actives' => 'boolean',
    ];

    // Relation avec les salaires
    public function salaires()
    {
        return $this->hasMany(Salaire::class, 'employe_id');
    }

    // Relation avec les primes
    public function primes()
    {
        return $this->hasMany(Prime::class, 'employe_id');
    }

    // Relation avec les retenues
    public function retenues()
    {
        return $this->hasMany(Retenue::class, 'employe_id');
    }

    // Relation avec les présences
    public function presences()
    {
        return $this->hasMany(\App\Models\Presence::class, 'employe_id');
    }

    // Accesseur pour le nom complet
    public function getNomCompletAttribute()
    {
        return $this->prenom . ' ' . $this->nom;
    }
}
