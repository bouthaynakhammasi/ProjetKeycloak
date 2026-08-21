<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    use HasFactory;

    protected $fillable = [
        'employe_id',
        'date',
        'heure_connexion',
        'heure_depart',
        'statut',
        'remarque',
    ];

    protected $appends = ['statut_label', 'badge_class'];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Relation avec Employe
     */
    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    /**
     * Scope : filtrer par date
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    /**
     * Scope : filtrer par statut
     */
    public function scopeByStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    /**
     * Accesseur : label du statut en français
     */
    public function getStatutLabelAttribute()
    {
        return match ($this->statut) {
            'present' => 'Présent',
            'retard'  => 'Retard',
            'absent'  => 'Absent',
            default   => $this->statut,
        };
    }

    /**
     * Accesseur : classes CSS du badge selon le statut
     */
    public function getBadgeClassAttribute()
    {
        return match ($this->statut) {
            'present' => 'bg-green-50 text-green-700 border-green-200',
            'retard'  => 'bg-yellow-50 text-yellow-700 border-yellow-200',
            'absent'  => 'bg-red-50 text-red-700 border-red-200',
            default   => 'bg-gray-50 text-gray-700 border-gray-200',
        };
    }
}
