<?php

namespace App\Events;

use App\Models\Salaire;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SalaireValidated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $salaire;
    public $employe;

    /**
     * Create a new event instance.
     */
    public function __construct(Salaire $salaire)
    {
        $this->salaire = $salaire;
        $this->salaire->load('employe');
        $this->employe = $this->salaire->employe;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        return [
            new Channel('salaries'),
            new PrivateChannel('employee.' . $this->employe->keycloak_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs()
    {
        return 'salaire.validated';
    }

    /**
     * Data to broadcast with the event.
     */
    public function broadcastWith()
    {
        return [
            'salaire_id' => $this->salaire->id,
            'employe_id' => $this->salaire->employe_id,
            'employe_nom' => $this->employe->nom_complet,
            'employe_email' => $this->employe->email,
            'mois' => $this->salaire->mois,
            'nom_mois' => $this->salaire->nom_mois,
            'annee' => $this->salaire->annee,
            'salaire_net' => $this->salaire->salaire_net,
            'statut_paiement' => $this->salaire->statut_paiement,
            'date_paiement' => $this->salaire->date_paiement ? $this->salaire->date_paiement->format('d/m/Y') : null,
        ];
    }
}
