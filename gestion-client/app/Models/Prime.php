<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prime extends Model
{
    use HasFactory;

    protected $fillable = [
        'employe_id',
        'type_prime',
        'montant',
        'description',
        'date',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date' => 'date',
    ];

    // Relation avec Employe
    public function employe()
    {
        return $this->belongsTo(Employe::class, 'employe_id');
    }

    // Scope pour filtrer par type de prime
    public function scopeType($query, $type)
    {
        return $query->where('type_prime', $type);
    }

    // Scope pour une période donnée
    public function scopePeriode($query, $debut, $fin)
    {
        return $query->whereBetween('date', [$debut, $fin]);
    }
}
