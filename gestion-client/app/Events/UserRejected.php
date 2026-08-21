<?php

namespace App\Events;

use App\Models\KeycloakUser;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UserRejected implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    /**
     * Create a new event instance.
     */
    public function __construct(KeycloakUser $user)
    {
        $this->user = $user;
        
        Log::info('UserRejected event created', [
            'email' => $user->email,
            'name' => $user->name,
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
        return 'user.rejected';
    }

    /**
     * Data to broadcast with this event.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->user->id,
            'keycloak_id' => $this->user->keycloak_id,
            'email' => $this->user->email,
            'name' => $this->user->name,
            'status' => $this->user->status,
        ];
    }
}
