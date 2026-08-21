<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeycloakUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'keycloak_id',
        'name',
        'email',
        'role',
        'status',
        'notified_at',
        'activated_at',
    ];

    protected $casts = [
        'notified_at'  => 'datetime',
        'activated_at' => 'datetime',
    ];

    /* ── Scopes ── */

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /* ── Helpers ── */

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'ROLE_ADMIN'    => 'Administrateur',
            'ROLE_EMPLOYEE' => 'Employé',
            default         => 'Aucun rôle',
        };
    }
}
