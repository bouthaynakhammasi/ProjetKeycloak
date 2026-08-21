<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'employe_id',
        'mois',
        'annee',
        'salaire_brut',
        'deductions',
        'salaire_net',
        'fichier_pdf',
    ];

    protected $casts = [
        'salaire_brut' => 'decimal:2',
        'deductions' => 'decimal:2',
        'salaire_net' => 'decimal:2',
    ];

    // Relation avec Employe
    public function employe()
    {
        return $this->belongsTo(Employe::class, 'employe_id');
    }

    // Calcul automatique du salaire net
    public function calculerSalaireNet()
    {
        $this->salaire_net = $this->salaire_brut - $this->deductions;
        $this->save();
    }

    // Scope pour filtrer par mois et année
    public function scopePeriode($query, $mois, $annee)
    {
        return $query->where('mois', $mois)->where('annee', $annee);
    }

    // Scope pour filtrer par employé
    public function scopeForEmploye($query, $employeId)
    {
        return $query->where('employe_id', $employeId);
    }

    // Accesseur pour le nom du mois
    public function getNomMoisAttribute()
    {
        $mois = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];
        return $mois[$this->mois] ?? '';
    }
}
