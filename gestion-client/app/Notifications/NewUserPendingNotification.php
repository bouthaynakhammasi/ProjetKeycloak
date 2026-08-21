<?php

namespace App\Notifications;

use App\Models\KeycloakUser;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserPendingNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly KeycloakUser $keycloakUser) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $adminUrl = url('/admin/users');
        return (new MailMessage)
            ->subject('🆕 Nouveau compte - action requise')
            ->greeting('Bonjour,')
            ->line('Nom : ' . $this->keycloakUser->name)
            ->line('Email : ' . $this->keycloakUser->email)
            ->line('Veuillez attribuer un rôle (ROLE_EMPLOYEE ou ROLE_ADMIN) via le tableau de bord admin.')
            ->action('Gérer les comptes', $adminUrl)
            ->salutation('Merci.');
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'name' => $this->keycloakUser->name,
            'email' => $this->keycloakUser->email,
            'registered_at' => $this->keycloakUser->created_at ? $this->keycloakUser->created_at->format('Y-m-d H:i:s') : null,
        ];
    }

}
