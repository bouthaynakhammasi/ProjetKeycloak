<?php

namespace App\Console\Commands;

use App\Models\CompteEnAttente;
use App\Services\KeycloakAdminService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupPendingUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:pending-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nettoie les entrées résiduelles dans comptes_en_attente pour les utilisateurs qui ont maintenant un rôle valide dans Keycloak';

    /**
     * Execute the console command.
     */
    public function handle(KeycloakAdminService $keycloak)
    {
        $this->info('Début du nettoyage des comptes en attente résiduels...');
        
        $pendingUsers = CompteEnAttente::where('statut', CompteEnAttente::STATUT_EN_ATTENTE)->get();
        $cleanedCount = 0;
        $errorCount = 0;

        foreach ($pendingUsers as $pending) {
            if (!$pending->keycloak_id) {
                $this->warn("Entrée sans keycloak_id ignorée : {$pending->email}");
                continue;
            }

            try {
                // Vérifier si l'utilisateur existe dans Keycloak
                if (!$keycloak->userExists($pending->keycloak_id)) {
                    $this->warn("Utilisateur Keycloak non trouvé : {$pending->email} (keycloak_id: {$pending->keycloak_id})");
                    continue;
                }

                // Récupérer les rôles de l'utilisateur via l'endpoint spécifique
                $rolesData = $keycloak->getUserRoles($pending->keycloak_id);
                $allRoles = collect($rolesData['all_roles'] ?? [])
                    ->map(fn ($r) => strtolower($r))
                    ->toArray();

                $this->line("Rôles détectés pour {$pending->email} :");
                $this->line("Realm roles: " . json_encode($rolesData['realm_roles'] ?? []));
                $this->line("Client roles: " . json_encode($rolesData['client_roles'] ?? []));
                $this->line("All roles: " . json_encode($allRoles));

                $isAdmin = collect($allRoles)->contains(fn ($r) => in_array($r, ['admin', 'role_admin'], true));
                $isEmploye = collect($allRoles)->contains(fn ($r) => in_array($r, ['employee', 'employe', 'role_employee', 'role_employe'], true));
                $hasValidRole = $isAdmin || $isEmploye;

                if ($hasValidRole) {
                    $this->info("Suppression de l'entrée résiduelle : {$pending->email} (rôle détecté)");
                    Log::info('CleanupPendingUsers: Deleting residual pending entry', [
                        'email' => $pending->email,
                        'keycloak_id' => $pending->keycloak_id,
                        'roles' => $allRoles,
                    ]);
                    $pending->delete();
                    $cleanedCount++;
                } else {
                    $this->line("Aucun rôle valide pour : {$pending->email} (conservé)");
                }
            } catch (\Exception $e) {
                $this->error("Erreur lors du traitement de {$pending->email} : " . $e->getMessage());
                Log::error('CleanupPendingUsers: Error processing pending user', [
                    'email' => $pending->email,
                    'error' => $e->getMessage(),
                ]);
                $errorCount++;
            }
        }

        $this->info('Nettoyage terminé.');
        $this->info("Entrées supprimées : {$cleanedCount}");
        $this->info("Erreurs : {$errorCount}");

        return Command::SUCCESS;
    }
}
