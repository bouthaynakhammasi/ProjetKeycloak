<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KeycloakAdminService
{
    private string $baseUrl;
    private string $realm;
    private string $clientId;
    private string $clientSecret;

    public function __construct()
    {
        $this->baseUrl      = rtrim(config('services.keycloak.base_url', 'http://localhost:8080'), '/');
        $this->realm        = config('services.keycloak.realms', 'CompanyRealm');
        $this->clientId     = config('services.keycloak.admin_client_id', 'laravel-admin-client');
        $this->clientSecret = config('services.keycloak.admin_client_secret', '');
    }

    /**
     * Obtenir un token d'accès admin via client_credentials.
     */
    public function getAdminToken(): ?string
    {
        try {
            Log::info('KeycloakAdminService: Requesting admin token', [
                'url' => "{$this->baseUrl}/realms/{$this->realm}/protocol/openid-connect/token",
                'client_id' => $this->clientId,
                'has_secret' => !empty($this->clientSecret),
            ]);

            $response = Http::asForm()->post(
                "{$this->baseUrl}/realms/{$this->realm}/protocol/openid-connect/token",
                [
                    'grant_type'    => 'client_credentials',
                    'client_id'     => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ]
            );

            Log::info('KeycloakAdminService: Token response', [
                'status' => $response->status(),
                'success' => $response->successful(),
            ]);

            if ($response->failed()) {
                Log::error('KeycloakAdminService: Failed to get admin token', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                    'client_id' => $this->clientId,
                ]);
                return null;
            }

            $token = $response->json('access_token');
            Log::info('KeycloakAdminService: Admin token obtained successfully');
            return $token;
        } catch (Exception $e) {
            Log::error('KeycloakAdminService: Exception getting admin token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Récupérer l'ID interne du rôle Realm dans Keycloak.
     */
    public function getRealmRole(string $token, string $roleName): ?array
    {
        try {
            Log::info('KeycloakAdminService: Fetching role', ['role' => $roleName]);

            $response = Http::withToken($token)
                ->get("{$this->baseUrl}/admin/realms/{$this->realm}/roles/{$roleName}");

            Log::info('KeycloakAdminService: Role fetch response', [
                'status' => $response->status(),
                'success' => $response->successful(),
            ]);

            if ($response->failed()) {
                Log::error("KeycloakAdminService: Role '{$roleName}' not found", [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('KeycloakAdminService: Exception getting role: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Assigner un rôle Realm à un utilisateur Keycloak.
     *
     * @param  string $keycloakUserId  L'ID Keycloak de l'utilisateur (sub du token JWT)
     * @param  string $roleName        Le nom du rôle (ex: ROLE_EMPLOYEE, ROLE_ADMIN)
     * @return bool
     */
    public function assignRole(string $keycloakUserId, string $roleName): bool
    {
        try {
            Log::info('KeycloakAdminService: Assigning role', [
                'userId' => $keycloakUserId,
                'role' => $roleName,
            ]);

            $token = $this->getAdminToken();
            if (!$token) {
                Log::error('KeycloakAdminService: Cannot assign role - no admin token');
                return false;
            }

            // Récupérer les détails du rôle (id + name requis par l'API)
            $role = $this->getRealmRole($token, $roleName);
            if (!$role) {
                Log::error("KeycloakAdminService: Role '{$roleName}' not found in Keycloak");
                return false;
            }

            Log::info('KeycloakAdminService: Role found, assigning to user', [
                'roleId' => $role['id'] ?? 'unknown',
                'roleName' => $role['name'] ?? 'unknown',
            ]);

            $response = Http::withToken($token)
                ->post(
                    "{$this->baseUrl}/admin/realms/{$this->realm}/users/{$keycloakUserId}/role-mappings/realm",
                    [['id' => $role['id'], 'name' => $role['name']]]
                );

            Log::info('KeycloakAdminService: Role assignment response', [
                'status' => $response->status(),
                'success' => $response->successful(),
            ]);

            if ($response->failed()) {
                Log::error("KeycloakAdminService: Failed to assign role '{$roleName}' to user '{$keycloakUserId}'", [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return false;
            }

            Log::info("KeycloakAdminService: Role '{$roleName}' assigned to user '{$keycloakUserId}'");
            return true;
        } catch (Exception $e) {
            Log::error('KeycloakAdminService: Exception assigning role: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Retirer un rôle Realm d'un utilisateur Keycloak.
     */
    public function removeRole(string $keycloakUserId, string $roleName): bool
    {
        try {
            $token = $this->getAdminToken();
            if (!$token) {
                return false;
            }

            $role = $this->getRealmRole($token, $roleName);
            if (!$role) {
                return false;
            }

            $response = Http::withToken($token)
                ->delete(
                    "{$this->baseUrl}/admin/realms/{$this->realm}/users/{$keycloakUserId}/role-mappings/realm",
                    [['id' => $role['id'], 'name' => $role['name']]]
                );

            return $response->successful();
        } catch (Exception $e) {
            Log::error('KeycloakAdminService: Exception removing role: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Lister tous les rôles Realm disponibles.
     */
    public function listRealmRoles(): array
    {
        try {
            $token = $this->getAdminToken();
            if (!$token) {
                return [];
            }

            $response = Http::withToken($token)
                ->get("{$this->baseUrl}/admin/realms/{$this->realm}/roles");

            return $response->successful() ? $response->json() : [];
        } catch (Exception $e) {
            Log::error('KeycloakAdminService: Exception listing roles: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Créer un utilisateur dans Keycloak.
     */
    public function createUser(array $userData): ?string
    {
        try {
            $token = $this->getAdminToken();
            if (!$token) {
                Log::error('KeycloakAdminService: Cannot get admin token');
                return null;
            }

            Log::info('KeycloakAdminService: Creating user', ['userData' => $userData]);

            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/admin/realms/{$this->realm}/users", $userData);

            if ($response->failed()) {
                Log::error('KeycloakAdminService: Failed to create user', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                    'userData' => $userData,
                ]);
                return null;
            }

            // Keycloak renvoie l'ID dans le header Location
            $location = $response->header('Location');
            if ($location) {
                $parts = explode('/', $location);
                $userId = end($parts);
                Log::info('KeycloakAdminService: User created successfully', ['userId' => $userId]);
                return $userId;
            }

            Log::error('KeycloakAdminService: No Location header in response');
            return null;
        } catch (Exception $e) {
            Log::error('KeycloakAdminService: Exception creating user: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Réinitialiser le mot de passe d'un utilisateur.
     */
    public function resetPassword(string $keycloakUserId, string $newPassword, bool $temporary = true): bool
    {
        try {
            $token = $this->getAdminToken();
            if (!$token) {
                return false;
            }

            $response = Http::withToken($token)
                ->put(
                    "{$this->baseUrl}/admin/realms/{$this->realm}/users/{$keycloakUserId}/reset-password",
                    [
                        'type'      => 'password',
                        'value'     => $newPassword,
                        'temporary' => $temporary,
                    ]
                );

            if ($response->failed()) {
                Log::error("KeycloakAdminService: Failed to reset password for user '{$keycloakUserId}'", [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return false;
            }

            Log::info("KeycloakAdminService: Password reset for user '{$keycloakUserId}'");
            return true;
        } catch (Exception $e) {
            Log::error('KeycloakAdminService: Exception resetting password: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprimer un utilisateur de Keycloak.
     */
    public function deleteUser(string $keycloakUserId): bool
    {
        try {
            $token = $this->getAdminToken();
            if (!$token) {
                return false;
            }

            $response = Http::withToken($token)
                ->delete("{$this->baseUrl}/admin/realms/{$this->realm}/users/{$keycloakUserId}");

            if ($response->failed()) {
                Log::error("KeycloakAdminService: Failed to delete user '{$keycloakUserId}'", [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return false;
            }

            Log::info("KeycloakAdminService: User '{$keycloakUserId}' deleted");
            return true;
        } catch (Exception $e) {
            Log::error('KeycloakAdminService: Exception deleting user: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtenir les rôles d'un utilisateur (realm + client).
     *
     * @param  string $keycloakUserId L'ID Keycloak de l'utilisateur
     * @return array Liste des rôles de l'utilisateur
     */
    public function getUserRoles(string $keycloakUserId): array
    {
        try {
            $token = $this->getAdminToken();
            if (!$token) {
                Log::error('KeycloakAdminService: Cannot get admin token for getUserRoles');
                return [];
            }

            // Récupérer les rôles realm
            $realmRolesResponse = Http::withToken($token)
                ->get("{$this->baseUrl}/admin/realms/{$this->realm}/users/{$keycloakUserId}/role-mappings/realm");

            $realmRoles = [];
            if ($realmRolesResponse->successful()) {
                $realmRoles = collect($realmRolesResponse->json())
                    ->pluck('name')
                    ->toArray();
            }

            // Récupérer les rôles client
            $clientId = config('services.keycloak.client_id', 'gestion-client');
            $clientRolesResponse = Http::withToken($token)
                ->get("{$this->baseUrl}/admin/realms/{$this->realm}/users/{$keycloakUserId}/role-mappings/clients/{$clientId}");

            $clientRoles = [];
            if ($clientRolesResponse->successful()) {
                $clientRoles = collect($clientRolesResponse->json())
                    ->pluck('name')
                    ->toArray();
            }

            return [
                'realm_roles' => $realmRoles,
                'client_roles' => $clientRoles,
                'all_roles' => array_merge($realmRoles, $clientRoles),
            ];
        } catch (Exception $e) {
            Log::error('KeycloakAdminService: Exception getting user roles: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtenir les détails d'un utilisateur.
     */
    public function getUserDetails(string $keycloakUserId): ?array
    {
        try {
            $token = $this->getAdminToken();
            if (!$token) {
                return null;
            }

            $response = Http::withToken($token)
                ->get("{$this->baseUrl}/admin/realms/{$this->realm}/users/{$keycloakUserId}");

            return $response->successful() ? $response->json() : null;
        } catch (Exception $e) {
            Log::error('KeycloakAdminService: Exception getting user details: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Lister tous les utilisateurs.
     */
    public function listUsers(): array
    {
        try {
            $token = $this->getAdminToken();
            if (!$token) {
                return [];
            }

            $response = Http::withToken($token)
                ->get("{$this->baseUrl}/admin/realms/{$this->realm}/users");

            return $response->successful() ? $response->json() : [];
        } catch (Exception $e) {
            Log::error('KeycloakAdminService: Exception listing users: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Désactiver un utilisateur.
     */
    public function disableUser(string $keycloakUserId): bool
    {
        try {
            $token = $this->getAdminToken();
            if (!$token) {
                return false;
            }

            $response = Http::withToken($token)
                ->put("{$this->baseUrl}/admin/realms/{$this->realm}/users/{$keycloakUserId}", [
                    'enabled' => false,
                ]);

            return $response->successful();
        } catch (Exception $e) {
            Log::error('KeycloakAdminService: Exception disabling user: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Activer un utilisateur.
     */
    public function enableUser(string $keycloakUserId): bool
    {
        try {
            $token = $this->getAdminToken();
            if (!$token) {
                return false;
            }

            $response = Http::withToken($token)
                ->put("{$this->baseUrl}/admin/realms/{$this->realm}/users/{$keycloakUserId}", [
                    'enabled' => true,
                ]);

            return $response->successful();
        } catch (Exception $e) {
            Log::error('KeycloakAdminService: Exception enabling user: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifier si un utilisateur existe dans Keycloak par son ID.
     *
     * @param  string $keycloakUserId L'ID Keycloak de l'utilisateur
     * @return bool True si l'utilisateur existe, false sinon
     */
    public function userExists(string $keycloakUserId): bool
    {
        try {
            $token = $this->getAdminToken();
            if (!$token) {
                Log::error('KeycloakAdminService: Cannot get admin token for userExists');
                return false;
            }

            $response = Http::withToken($token)
                ->get("{$this->baseUrl}/admin/realms/{$this->realm}/users/{$keycloakUserId}");

            if ($response->status() === 404) {
                Log::info('KeycloakAdminService: User not found in Keycloak', ['userId' => $keycloakUserId]);
                return false;
            }

            return $response->successful();
        } catch (Exception $e) {
            Log::error('KeycloakAdminService: Exception checking user existence: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Rechercher un utilisateur dans Keycloak par son email.
     *
     * @param  string $email L'email de l'utilisateur à rechercher
     * @return string|null L'ID Keycloak de l'utilisateur ou null si non trouvé
     */
    public function findUserByEmail(string $email): ?string
    {
        try {
            $token = $this->getAdminToken();
            if (!$token) {
                Log::error('KeycloakAdminService: Cannot get admin token for findUserByEmail');
                return null;
            }

            Log::info('KeycloakAdminService: Searching user by email', ['email' => $email]);

            $response = Http::withToken($token)
                ->get("{$this->baseUrl}/admin/realms/{$this->realm}/users", [
                    'email' => $email,
                    'exact' => true,
                ]);

            Log::info('KeycloakAdminService: User search response', [
                'status' => $response->status(),
                'success' => $response->successful(),
            ]);

            if ($response->failed()) {
                Log::error("KeycloakAdminService: Failed to search user with email '{$email}'", [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $users = $response->json();
            if (empty($users) || !is_array($users)) {
                Log::info("KeycloakAdminService: No user found with email '{$email}'");
                return null;
            }

            // Retourner l'ID du premier utilisateur trouvé
            $userId = $users[0]['id'] ?? null;
            if ($userId) {
                Log::info("KeycloakAdminService: User found", ['userId' => $userId, 'email' => $email]);
            }

            return $userId;
        } catch (Exception $e) {
            Log::error('KeycloakAdminService: Exception finding user by email: ' . $e->getMessage());
            return null;
        }
    }
}
