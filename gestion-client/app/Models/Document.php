<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'employe_id',
        'type',
        'fichier',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    // Relation avec Employe
    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    // Scope pour filtrer par type
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Scope pour filtrer par employé
    public function scopeForEmploye($query, $employeId)
    {
        return $query->where('employe_id', $employeId);
    }
}
