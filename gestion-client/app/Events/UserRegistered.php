<?php

namespace App\Events;

use App\Models\CompteEnAttente;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UserRegistered implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pendingUser;

    /**
     * Create a new event instance.
     */
    public function __construct(CompteEnAttente $pendingUser)
    {
        $this->pendingUser = $pendingUser;
        
        Log::info('UserRegistered event created', [
            'email' => $pendingUser->email,
            'name' => $pendingUser->prenom . ' ' . $pendingUser->nom,
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('admin-users'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'user.registered';
    }

    /**
     * Data to broadcast with this event.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->pendingUser->id,
            'email' => $this->pendingUser->email,
            'nom' => $this->pendingUser->nom,
            'prenom' => $this->pendingUser->prenom,
            'statut' => $this->pendingUser->statut,
            'created_at' => $this->pendingUser->created_at->toIso8601String(),
        ];
    }
}
