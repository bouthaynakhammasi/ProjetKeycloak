<?php

namespace App\Models;

use App\Enums\AbsenceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use HasFactory;

    protected $fillable = [
        'employe_id',
        'type',
        'date_debut',
        'date_fin',
        'nombre_jours',
        'motif',
        'statut',
        'reponse_at',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'reponse_at' => 'datetime',
        'statut' => AbsenceStatus::class,
    ];

    // Relation avec Employe
    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    // Scope pour filtrer par statut
    public function scopeByStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    // Scope pour filtrer par employé
    public function scopeForEmploye($query, $employeId)
    {
        return $query->where('employe_id', $employeId);
    }

    // Scope pour les absences en attente
    public function scopePending($query)
    {
        return $query->where('statut', AbsenceStatus::PENDING->value);
    }

    // Scope pour les absences approuvées
    public function scopeApproved($query)
    {
        return $query->where('statut', AbsenceStatus::APPROVED->value);
    }

    // Scope pour les absences refusées
    public function scopeRejected($query)
    {
        return $query->where('statut', AbsenceStatus::REJECTED->value);
    }

    // Accesseur pour le statut en français
    public function getStatutLabelAttribute()
    {
        return $this->statut?->getLabel() ?? $this->statut;
    }

    // Accesseur pour la classe du badge
    public function getBadgeClassAttribute()
    {
        return $this->statut?->getBadgeClass() ?? 'bg-gray-100 text-gray-700';
    }

    // Calculer le nombre de jours automatiquement
    public function calculateNombreJours()
    {
        $debut = \Carbon\Carbon::parse($this->date_debut);
        $fin = \Carbon\Carbon::parse($this->date_fin);
        $this->nombre_jours = $debut->diffInDays($fin) + 1;
        $this->save();
    }
}
