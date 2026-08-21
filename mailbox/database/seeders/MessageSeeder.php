<?php

namespace Database\Seeders;

use App\Models\Message;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    /**
     * Peuple la base avec des messages de démonstration.
     * Remplacez $demoUserId par le 'sub' Keycloak de votre utilisateur de test.
     */
    public function run(): void
    {
        // Remplacez cette valeur par le vrai `sub` Keycloak de l'utilisateur de test
        $demoUserId = env('DEMO_KEYCLOAK_USER_ID', 'demo-user-sub-placeholder');

        $messages = [
            [
                'keycloak_user_id' => $demoUserId,
                'sender_name'      => 'Keycloak Admin',
                'sender_email'     => 'admin@keycloak.local',
                'subject'          => 'Réinitialisation de mot de passe confirmée',
                'body'             => "Bonjour,\n\nVotre mot de passe a bien été réinitialisé via Keycloak SSO.\n\nSi vous n'êtes pas à l'origine de cette demande, contactez l'administrateur immédiatement.\n\nCordialement,\nL'équipe Keycloak",
                'folder'           => 'inbox',
                'is_read'          => false,
                'is_starred'       => false,
                'created_at'       => now()->subMinutes(23),
            ],
            [
                'keycloak_user_id' => $demoUserId,
                'sender_name'      => 'Mailbox',
                'sender_email'     => 'noreply@mailbox.local',
                'subject'          => 'Bienvenue sur votre espace, connexion SSO active',
                'body'             => "Bonjour,\n\nVotre compte a été connecté avec succès à l'application Mailbox via l'authentification SSO Keycloak (Realm : CompanyRealm).\n\nVous pouvez désormais accéder à tous vos espaces sans avoir à vous reconnecter.\n\nBonne journée !",
                'folder'           => 'inbox',
                'is_read'          => true,
                'is_starred'       => true,
                'created_at'       => now()->subHours(1)->subMinutes(13),
            ],
            [
                'keycloak_user_id' => $demoUserId,
                'sender_name'      => 'Louis Fournier',
                'sender_email'     => 'l.fournier@company.local',
                'subject'          => 'Question sur le module Absences',
                'body'             => "Bonjour,\n\nJ'aurais besoin de votre avis sur la mise à jour du module Absences prévue pour la semaine prochaine.\nPouvez-vous me confirmer les dates de validation ?\n\nMerci d'avance,\nLouis",
                'folder'           => 'inbox',
                'is_read'          => false,
                'is_starred'       => false,
                'created_at'       => now()->subDay()->subHours(2),
            ],
            [
                'keycloak_user_id' => $demoUserId,
                'sender_name'      => 'Docker Desktop',
                'sender_email'     => 'noreply@docker.com',
                'subject'          => 'Rapport hebdomadaire des conteneurs',
                'body'             => "Rapport hebdomadaire Docker :\n\n- Conteneurs actifs : 8\n- Images téléchargées : 3\n- Espaces disque utilisés : 12.4 GB\n\nAucune anomalie détectée cette semaine.\n\nDocker Desktop",
                'folder'           => 'inbox',
                'is_read'          => false,
                'is_starred'       => false,
                'created_at'       => now()->startOfWeek()->addDays(1)->setHour(8)->setMinute(0),
            ],
            [
                'keycloak_user_id' => $demoUserId,
                'sender_name'      => 'Vous',
                'sender_email'     => 'me@mailbox.local',
                'subject'          => 'Compte rendu réunion projet SSO',
                'body'             => "Bonjour à tous,\n\nVeuillez trouver ci-joint le compte rendu de la réunion du 08/07 concernant l'avancement du projet SSO Keycloak.\n\nPoints abordés :\n1. Intégration Mailbox ✓\n2. Intégration Security ✓\n3. Déploiement en production : prévu le 15/07\n\nBonne lecture,",
                'folder'           => 'sent',
                'is_read'          => true,
                'is_starred'       => false,
                'created_at'       => now()->subDays(2)->setHour(14)->setMinute(30),
            ],
            [
                'keycloak_user_id' => $demoUserId,
                'sender_name'      => 'Vous',
                'sender_email'     => 'me@mailbox.local',
                'subject'          => '[Brouillon] Proposition de charte graphique',
                'body'             => "Bonjour,\n\nJe réfléchis à une nouvelle charte graphique pour l'interface…\n\n[À compléter]",
                'folder'           => 'drafts',
                'is_read'          => true,
                'is_starred'       => false,
                'created_at'       => now()->subHours(5),
            ],
        ];

        foreach ($messages as $messageData) {
            Message::create($messageData);
        }

        $this->command->info('✅ ' . count($messages) . ' messages de démonstration créés pour l\'utilisateur : ' . $demoUserId);
    }
}
