<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employe_id',
        'type_conge',
        'date_debut',
        'date_fin',
        'motif',
        'status',
        'approuve_par',
        'event_id',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin'   => 'date',
    ];

    public function employe()
    {
        return $this->belongsTo(Employe::class, 'employe_id');
    }

    public function approver()
    {
        return $this->belongsTo(Employe::class, 'approuve_par');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function approuver($adminId = null)
    {
        $this->status = 'accepte';
        if ($adminId) {
            $this->approuve_par = $adminId;
        }

        // Créer l'événement dans l'agenda
        $event = Event::create([
            'title' => 'Congé ' . ucfirst($this->type_conge),
            'type' => 'conge',
            'employe_id' => $this->employe_id,
            'start_date' => $this->date_debut,
            'end_date' => $this->date_fin,
            'description' => $this->motif,
        ]);

        $this->event_id = $event->id;
        $this->save();
    }
}
