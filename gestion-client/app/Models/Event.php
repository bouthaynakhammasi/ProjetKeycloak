<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'employe_id',
        'start_date',
        'end_date',
        'start_time',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    /**
     * Relation avec l'employé (optionnelle)
     */
    public function employe()
    {
        return $this->belongsTo(Employe::class, 'employe_id');
    }

    /**
     * Alias pour employee
     */
    public function employee()
    {
        return $this->employe();
    }

    /**
     * Scope pour filtrer les événements d'un mois et année donnés
     */
    public function scopeDuMois($query, $month, $year = null)
    {
        $year = $year ?? date('Y');

        return $query->where(function ($q) use ($month, $year) {
            $q->whereMonth('start_date', $month)->whereYear('start_date', $year)
              ->orWhere(function ($sub) use ($month, $year) {
                  $sub->whereNotNull('end_date')
                      ->whereMonth('end_date', $month)
                      ->whereYear('end_date', $year);
              });
        });
    }

    /**
     * Scope pour filtrer par type
     */
    public function scopeParType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Libellé lisible en français
     */
    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'conge'     => 'Congé',
            'formation' => 'Formation',
            'entretien' => 'Entretien',
            'ferie'     => 'Férié',
            'reunion'   => 'Réunion',
            default     => ucfirst($this->type),
        };
    }

    /**
     * Pastille de couleur (bullet)
     */
    public function getDotColorAttribute()
    {
        return match ($this->type) {
            'conge'     => 'bg-red-500',
            'formation' => 'bg-blue-500',
            'entretien' => 'bg-orange-500',
            'ferie'     => 'bg-green-500',
            'reunion'   => 'bg-purple-500',
            default     => 'bg-gray-500',
        };
    }

    /**
     * Badge coloré (fond + texte + bordure)
     */
    public function getBadgeClassAttribute()
    {
        return match ($this->type) {
            'conge'     => 'bg-red-50 text-red-700 border-red-200',
            'formation' => 'bg-blue-50 text-blue-700 border-blue-200',
            'entretien' => 'bg-orange-50 text-orange-700 border-orange-200',
            'ferie'     => 'bg-green-50 text-green-700 border-green-200',
            'reunion'   => 'bg-purple-50 text-purple-700 border-purple-200',
            default     => 'bg-gray-50 text-gray-700 border-gray-200',
        };
    }
}
