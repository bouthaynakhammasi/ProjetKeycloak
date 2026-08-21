<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KeycloakUser;
use App\Models\CompteEnAttente;
use App\Models\Employe;
use App\Services\KeycloakAdminService;
use App\Events\UserValidated;
use App\Events\UserRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserManagementController extends Controller
{
    public function __construct(private readonly KeycloakAdminService $keycloak) {}

    /**
     * Liste tous les utilisateurs (pending, active, rejected).
     * Nettoie automatiquement les entrées orphelines (utilisateurs supprimés dans Keycloak).
     */
    public function index()
    {
        // Nettoyer les utilisateurs orphelins dans keycloak_users
        $activeUsers = KeycloakUser::active()->orderByDesc('activated_at')->get();
        foreach ($activeUsers as $user) {
            if ($user->keycloak_id && !$this->keycloak->userExists($user->keycloak_id)) {
                Log::info('UserManagementController: Deleting orphaned user from keycloak_users', [
                    'email' => $user->email,
                    'keycloak_id' => $user->keycloak_id,
                ]);
                $user->delete();
            }
        }

        // Nettoyer les utilisateurs orphelins dans comptes_en_attente
        $pendingUsers = CompteEnAttente::where('statut', CompteEnAttente::STATUT_EN_ATTENTE)->orderByDesc('created_at')->get();
        foreach ($pendingUsers as $pending) {
            if ($pending->keycloak_id && !$this->keycloak->userExists($pending->keycloak_id)) {
                Log::info('UserManagementController: Deleting orphaned user from comptes_en_attente', [
                    'email' => $pending->email,
                    'keycloak_id' => $pending->keycloak_id,
                ]);
                $pending->delete();
            }
        }

        // Recharger les listes après nettoyage
        $pendingUsers  = CompteEnAttente::where('statut', CompteEnAttente::STATUT_EN_ATTENTE)->orderByDesc('created_at')->get();
        $activeUsers   = KeycloakUser::active()->orderByDesc('activated_at')->get();
        $rejectedUsers = KeycloakUser::where('status', 'rejected')->orderByDesc('updated_at')->get();

        return view('admin.users.index', compact('pendingUsers', 'activeUsers', 'rejectedUsers'));
    }

    /**
     * Valider un compte en attente avec logique hybride (recherche ou création).
     */
    public function validateAndCreate(Request $request, CompteEnAttente $pendingUser)
    {
        $request->validate([
            'role' => ['required', 'in:ROLE_ADMIN,ROLE_EMPLOYEE,ROLE_CLIENT'],
        ]);

        $role = $request->input('role');
        $keycloakUserId = null;
        $userCreated = false;

        // 1. Rechercher l'utilisateur dans Keycloak par son email
        $keycloakUserId = $this->keycloak->findUserByEmail($pendingUser->email);

        if ($keycloakUserId) {
            // Utilisateur existe déjà dans Keycloak
            Log::info('UserManagementController: User found in Keycloak', [
                'email' => $pendingUser->email,
                'keycloakUserId' => $keycloakUserId,
            ]);
        } else {
            // Utilisateur n'existe pas → le créer
            Log::info('UserManagementController: User not found, creating in Keycloak', [
                'email' => $pendingUser->email,
                'name' => $pendingUser->prenom . ' ' . $pendingUser->nom,
            ]);

            $keycloakUserId = $this->keycloak->createUser([
                'username' => $pendingUser->email,
                'email'     => $pendingUser->email,
                'firstName' => $pendingUser->prenom,
                'lastName'  => $pendingUser->nom,
                'enabled'   => true,
                'emailVerified' => true,
            ]);

            if (!$keycloakUserId) {
                Log::error('UserManagementController: Failed to create user in Keycloak', [
                    'email' => $pendingUser->email,
                ]);
                return redirect()->route('admin.users.index')
                    ->with('error', 'Erreur lors de la création de l\'utilisateur dans Keycloak. Vérifiez les logs pour plus de détails.');
            }

            $userCreated = true;
            Log::info('UserManagementController: User created successfully in Keycloak', [
                'userId' => $keycloakUserId,
            ]);
        }

        // 2. Activer l'utilisateur dans Keycloak si nécessaire
        $userEnabled = $this->keycloak->enableUser($keycloakUserId);
        if (!$userEnabled) {
            Log::warning('UserManagementController: Failed to enable user in Keycloak', ['userId' => $keycloakUserId]);
        }

        // 3. Assigner le rôle dans Keycloak
        $roleAssigned = $this->keycloak->assignRole($keycloakUserId, $role);
        if (!$roleAssigned) {
            Log::warning('UserManagementController: Failed to assign role in Keycloak', ['userId' => $keycloakUserId, 'role' => $role]);
        }

        // 4. Créer ou mettre à jour l'entrée dans keycloak_users (synchronisation)
        $keycloakUser = KeycloakUser::updateOrCreate(
            ['email' => $pendingUser->email],
            [
                'keycloak_id' => $keycloakUserId,
                'name'        => $pendingUser->prenom . ' ' . $pendingUser->nom,
                'role'        => $role,
                'status'      => 'active',
                'activated_at' => now(),
            ]
        );

        // 5. Créer l'entrée dans employes si le rôle est ROLE_EMPLOYEE
        if ($role === 'ROLE_EMPLOYEE') {
            Employe::updateOrCreate(
                ['email' => $pendingUser->email],
                [
                    'nom' => $pendingUser->nom,
                    'prenom' => $pendingUser->prenom,
                    'email' => $pendingUser->email,
                    'poste' => 'Employé',
                    'statut' => 'actif',
                    'keycloak_id' => $keycloakUserId,
                ]
            );
            Log::info('UserManagementController: Employe entry created', [
                'email' => $pendingUser->email,
                'keycloak_id' => $keycloakUserId,
            ]);
        }

        // 6. Supprimer l'entrée de comptes_en_attente
        $pendingUser->delete();

        // 6. Broadcast event for real-time admin dashboard update
        broadcast(new UserValidated($keycloakUser));

        $roleLabel = match($role) {
            'ROLE_ADMIN' => 'Administrateur',
            'ROLE_EMPLOYEE' => 'Employé',
            'ROLE_CLIENT' => 'Client',
            default => $role,
        };

        $message = "Compte validé avec succès. {$roleLabel} ";
        if ($userCreated) {
            $message .= "créé et activé dans Keycloak";
        } else {
            $message .= "activé dans Keycloak (utilisateur existant)";
        }

        if (!$roleAssigned) {
            $message .= " (Attention : l'assignation du rôle a échoué dans Keycloak, mais le rôle est enregistré en base)";
        }

        return redirect()->route('admin.users.index')
            ->with('success', $message);
    }

    /**
     * Attribuer un rôle à un utilisateur via l'API Admin Keycloak.
     */
    public function assignRole(Request $request, KeycloakUser $user)
    {
        $request->validate([
            'role' => ['required', 'in:ROLE_ADMIN,ROLE_EMPLOYEE,ROLE_CLIENT'],
        ]);

        $newRole = $request->input('role');

        // Retirer l'ancien rôle si présent
        if ($user->role && $user->role !== $newRole) {
            $this->keycloak->removeRole($user->keycloak_id, $user->role);
        }

        // Assigner le nouveau rôle dans Keycloak
        $success = $this->keycloak->assignRole($user->keycloak_id, $newRole);

        if (!$success) {
            Log::warning("UserManagementController: Keycloak API failed for user {$user->keycloak_id}, updating DB only.");
            // On continue quand même pour mettre à jour la base
        }

        // Mettre à jour la base de données
        $user->update([
            'role'         => $newRole,
            'status'       => 'active',
            'activated_at' => now(),
        ]);

        $roleLabel = match($newRole) {
            'ROLE_ADMIN' => 'Administrateur',
            'ROLE_EMPLOYEE' => 'Employé',
            'ROLE_CLIENT' => 'Client',
            default => $newRole,
        };
        $apiNote   = $success ? '' : ' (API Keycloak indisponible — rôle enregistré en base uniquement)';

        return redirect()->route('admin.users.index')
            ->with('success', "Le rôle {$roleLabel} a été attribué à {$user->name}.{$apiNote}");
    }

    /**
     * Rejeter un compte en attente.
     */
    public function reject(CompteEnAttente $pendingUser)
    {
        // Create a rejected user entry for tracking
        $rejectedUser = KeycloakUser::create([
            'keycloak_id' => $pendingUser->keycloak_id ?? null,
            'name'        => $pendingUser->prenom . ' ' . $pendingUser->nom,
            'email'       => $pendingUser->email,
            'role'        => null,
            'status'      => 'rejected',
        ]);

        $pendingUser->delete();

        // Broadcast event for real-time admin dashboard update
        broadcast(new UserRejected($rejectedUser));

        return redirect()->route('admin.users.index')
            ->with('success', "Le compte de {$pendingUser->prenom} {$pendingUser->nom} a été rejeté.");
    }

    /**
     * Réactiver un compte rejeté ou changer le rôle d'un compte actif.
     */
    public function reactivate(KeycloakUser $user)
    {
        $user->update(['status' => 'pending']);

        return redirect()->route('admin.users.index')
            ->with('success', "Le compte de {$user->name} a été remis en attente de validation.");
    }

    /**
     * Mettre à jour le rôle d'un utilisateur actif.
     */
    public function updateRole(Request $request, KeycloakUser $user)
    {
        $request->validate([
            'role' => ['required', 'in:ROLE_ADMIN,ROLE_EMPLOYEE,ROLE_CLIENT'],
        ]);

        $newRole = $request->input('role');

        // Retirer l'ancien rôle si présent
        if ($user->role && $user->role !== $newRole) {
            $this->keycloak->removeRole($user->keycloak_id, $user->role);
        }

        // Assigner le nouveau rôle dans Keycloak
        $success = $this->keycloak->assignRole($user->keycloak_id, $newRole);

        // Mettre à jour la base de données
        $user->update(['role' => $newRole]);

        $roleLabel = match($newRole) {
            'ROLE_ADMIN' => 'Administrateur',
            'ROLE_EMPLOYEE' => 'Employé',
            'ROLE_CLIENT' => 'Client',
            default => $newRole,
        };

        return redirect()->route('admin.users.index')
            ->with('success', "Rôle mis à jour : {$roleLabel}");
    }

    /**
     * Basculer le statut (activer/désactiver) d'un utilisateur.
     */
    public function toggleStatus(KeycloakUser $user)
    {
        $isEnabled = $user->status === 'active';
        
        if ($isEnabled) {
            $this->keycloak->disableUser($user->keycloak_id);
            $user->update(['status' => 'inactive']);
            return redirect()->route('admin.users.index')
                ->with('success', "Compte désactivé");
        } else {
            $this->keycloak->enableUser($user->keycloak_id);
            $user->update(['status' => 'active']);
            return redirect()->route('admin.users.index')
                ->with('success', "Compte activé");
        }
    }

    /**
     * Supprimer un utilisateur.
     */
    public function destroy(KeycloakUser $user)
    {
        // Supprimer de Keycloak
        $this->keycloak->deleteUser($user->keycloak_id);

        // Supprimer de la base
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Compte supprimé définitivement");
    }

    /**
     * Afficher les détails d'un utilisateur.
     */
    public function show(KeycloakUser $user)
    {
        $keycloakDetails = $this->keycloak->getUserDetails($user->keycloak_id);

        return view('admin.users.show', compact('user', 'keycloakDetails'));
    }

    /**
     * Réinitialiser le mot de passe d'un utilisateur.
     */
    public function resetPassword(KeycloakUser $user)
    {
        $temporaryPassword = Str::random(12);
        $success = $this->keycloak->resetPassword($user->keycloak_id, $temporaryPassword, true);

        if ($success) {
            return redirect()->route('admin.users.index')
                ->with('success', "Mot de passe réinitialisé. Nouveau mot de passe temporaire : {$temporaryPassword}");
        } else {
            return redirect()->route('admin.users.index')
                ->with('error', "Erreur lors de la réinitialisation du mot de passe");
        }
    }
}
