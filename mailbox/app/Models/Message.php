<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'keycloak_user_id',
        'recipient_id',
        'recipient_email',
        'sender_name',
        'sender_email',
        'subject',
        'body',
        'folder',
        'is_read',
        'is_starred',
    ];

    protected $casts = [
        'is_read'    => 'boolean',
        'is_starred' => 'boolean',
    ];

    // ─── Scopes ───────────────────────────────────────────────────────────────

    /**
     * Filtre les messages de la boîte de réception.
     */
    public function scopeInbox(Builder $query): Builder
    {
        return $query->where('folder', 'inbox');
    }

    /**
     * Filtre les messages non lus.
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    /**
     * Filtre les messages marqués comme favoris.
     */
    public function scopeStarred(Builder $query): Builder
    {
        return $query->where('is_starred', true);
    }

    /**
     * Filtre les messages d'un dossier donné.
     */
    public function scopeInFolder(Builder $query, string $folder): Builder
    {
        return $query->where('folder', $folder);
    }

    /**
     * Filtre les messages appartenant à l'utilisateur Keycloak connecté.
     */
    public function scopeForUser(Builder $query, string $keycloakUserId): Builder
    {
        return $query->where('keycloak_user_id', $keycloakUserId);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Retourne la date au format relatif lisible (09:12 / Hier / Lun / dd/mm).
     */
    public function getRelativeDateAttribute(): string
    {
        $date = $this->created_at;
        $now  = now();

        if ($date->isToday()) {
            return $date->format('H:i');
        }

        if ($date->isYesterday()) {
            return 'Hier';
        }

        if ($date->greaterThanOrEqualTo($now->copy()->startOfWeek())) {
            $days = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
            return $days[$date->dayOfWeek];
        }

        return $date->format('d/m');
    }

    /**
     * Indique si le message est non lu.
     */
    public function getIsUnreadAttribute(): bool
    {
        return !$this->is_read;
    }
}
