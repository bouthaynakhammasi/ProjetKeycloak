<?php

namespace App\Http\Controllers;

use App\Models\KeycloakUser;
use App\Models\CompteEnAttente;
use App\Models\Employe;
use App\Notifications\NewUserPendingNotification;
use App\Events\UserRegistered;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class AuthController extends Controller
{
    /**
     * Redirige l'utilisateur vers la page de connexion Keycloak.
     */
    public function login(Request $request)
    {
        try {
            if ($request->has('redirect')) {
                session(['url.intended' => $request->get('redirect')]);
            }

            return Socialite::driver('keycloak')
                ->redirectUrl(config('services.keycloak.redirect'))
                ->redirect();
        } catch (Exception $e) {
            Log::error('Keycloak redirect error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Impossible de contacter le serveur d\'authentification : ' . $e->getMessage());
        }
    }

    /**
     * Gère le retour de Keycloak après authentification.
     */
    public function callback(Request $request)
    {
        // 1. Vérifier si Keycloak a renvoyé une erreur dans l'URL
        if ($request->has('error')) {
            $errorDescription = $request->query('error_description', 'L\'authentification a été annulée ou a échoué.');
            return redirect()->route('login')->with('error', 'Erreur Keycloak : ' . $errorDescription);
        }

        try {
            $user = Socialite::driver('keycloak')->user();

            if (!$user || !$user->getId()) {
                return redirect()->route('login')->with('error', 'Utilisateur introuvable ou jeton invalide.');
            }

            // ── 1. Extraire les rôles depuis le JWT ──────────────────────────────
            $rawData      = $user->user ?? $user->getRaw();
            $realmRoles   = data_get($rawData, 'realm_access.roles', []);
            $clientId     = config('services.keycloak.client_id', 'gestion-client');
            $clientRoles  = data_get($rawData, "resource_access.{$clientId}.roles", []);
            $allRoles     = collect(array_merge($realmRoles, $clientRoles))
                ->map(fn ($r) => strtolower($r))
                ->toArray();

            $isAdmin   = collect($allRoles)->contains(fn ($r) => in_array($r, ['admin', 'role_admin'], true));
            $isEmploye = collect($allRoles)->contains(fn ($r) => in_array($r, ['employee', 'employe', 'role_employee', 'role_employe'], true));
            $detectedRole = $isAdmin ? 'ROLE_ADMIN' : ($isEmploye ? 'ROLE_EMPLOYEE' : null);
            
            // If a valid role is detected, clean up any residual entry in comptes_en_attente
            if ($detectedRole) {
                $pendingEntry = CompteEnAttente::where('keycloak_id', $user->getId())->first();
                if ($pendingEntry) {
                    Log::info('AuthController: Cleaning up residual pending entry for user with valid role', [
                        'email' => $user->getEmail(),
                        'keycloak_id' => $user->getId(),
                        'detected_role' => $detectedRole,
                    ]);
                    $pendingEntry->delete();
                }
            }
            
            // If no valid role detected, record pending account for admin review
            if (! $detectedRole) {
                $fullName = $user->getName() ?? $user->getNickname() ?? '';
                $parts = explode(' ', $fullName);
                $prenom = $parts[0] ?? null;
                $nom = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : $fullName;

                $pendingUser = CompteEnAttente::updateOrCreate(
                    ['keycloak_id' => $user->getId()],
                    [
                        'nom'    => $nom,
                        'prenom' => $prenom,
                        'email'  => $user->getEmail(),
                        'statut'=> CompteEnAttente::STATUT_EN_ATTENTE,
                    ]
                );

                // Broadcast event for real-time admin dashboard update
                try {
                    broadcast(new UserRegistered($pendingUser));
                } catch (Exception $e) {
                    Log::error('Broadcast UserRegistered failed: ' . $e->getMessage());
                    // Continue without broadcasting - don't fail the entire auth flow
                }
            }

            // ── 2. Rechercher ou créer l'entrée dans keycloak_users ──────────────
            $keycloakUser = KeycloakUser::firstWhere('email', $user->getEmail());

            if ($keycloakUser) {
                // Met à jour les champs susceptibles d'avoir changé
                $keycloakUser->update([
                    'keycloak_id' => $user->getId(),
                    'name'        => $user->getName() ?? $user->getNickname() ?? $keycloakUser->name,
                ]);
            } else {
                // Nouvel utilisateur — créer avec statut pending
                $keycloakUser = KeycloakUser::create([
                    'keycloak_id' => $user->getId(),
                    'name'        => $user->getName() ?? $user->getNickname() ?? 'Utilisateur',
                    'email'       => $user->getEmail(),
                    'role'        => null,
                    'status'      => 'pending',
                ]);

                // Notifier l'administrateur uniquement à la création
                $adminEmail = config('services.keycloak.admin_notification_email', 'admin@gestion-client.com');
                try {
                    Notification::route('mail', $adminEmail)
                        ->notify(new NewUserPendingNotification($keycloakUser));
                    $keycloakUser->update(['notified_at' => now()]);
                } catch (Exception $e) {
                    Log::error('Notification admin failed: ' . $e->getMessage());
                }
            }

            // ── 3. Détermination du rôle avec priorité Keycloak ───────────────────
            // RÈGLE : ROLE_ADMIN dans Keycloak = autorisation immédiate, ignore statut Laravel
            if ($detectedRole === 'ROLE_ADMIN') {
                // Auto-activer si pending et définir le rôle admin
                if ($keycloakUser->isPending()) {
                    $keycloakUser->update([
                        'role'         => 'ROLE_ADMIN',
                        'status'       => 'active',
                        'activated_at' => now(),
                    ]);
                    $keycloakUser->refresh();
                }
                $sessionRole = 'ROLE_ADMIN';
                Log::info('AuthController: Admin authenticated via Keycloak role', [
                    'email' => $user->getEmail(),
                    'keycloak_id' => $user->getId(),
                ]);
            }
            // RÈGLE : ROLE_EMPLOYEE dans Keycloak = vérifier statut Laravel
            elseif ($detectedRole === 'ROLE_EMPLOYEE') {
                // Si l'utilisateur a un rôle local valide et actif, faire confiance à ça
                if ($keycloakUser->role === 'ROLE_EMPLOYEE' && $keycloakUser->isActive()) {
                    $sessionRole = 'ROLE_EMPLOYEE';
                }
                // Si Keycloak a ROLE_EMPLOYEE mais compte pending → auto-activer
                elseif ($keycloakUser->isPending()) {
                    $keycloakUser->update([
                        'role'         => 'ROLE_EMPLOYEE',
                        'status'       => 'active',
                        'activated_at' => now(),
                    ]);
                    $keycloakUser->refresh();
                    $sessionRole = 'ROLE_EMPLOYEE';
                }
                // Sinon, utiliser le rôle détecté si actif
                elseif ($keycloakUser->isActive()) {
                    $sessionRole = 'ROLE_EMPLOYEE';
                } else {
                    $sessionRole = null;
                }
            }
            // RÈGLE : Autres rôles ou aucun rôle détecté
            else {
                // Si l'utilisateur a un rôle local valide et actif, faire confiance à ça
                if ($keycloakUser->role && $keycloakUser->isActive()) {
                    $sessionRole = $keycloakUser->role;
                }
                // Sinon, utiliser le rôle détecté dans le token Keycloak
                elseif ($detectedRole) {
                    if ($keycloakUser->isPending()) {
                        $keycloakUser->update([
                            'role'         => $detectedRole,
                            'status'       => 'active',
                            'activated_at' => now(),
                        ]);
                        $keycloakUser->refresh();
                    }
                    $sessionRole = $detectedRole;
                } else {
                    $sessionRole = null;
                }
            }

            // Stocker les informations en session
            session([
                'user_id'    => $user->getId(),
                'user_name'  => $keycloakUser->name,
                'user_email' => $keycloakUser->email,
                'user_role'  => $sessionRole,
                'id_token'   => $user->accessTokenResponseBody['id_token'] ?? $user->token,
            ]);

            // Rechercher l'employé correspondant et stocker sa photo en session
            $employe = Employe::where('keycloak_id', $user->getId())->first();
            if ($employe && $employe->photo) {
                session(['user_photo' => $employe->photo]);
            }

            // Redirections basées sur le statut et le rôle
            // RÈGLE : ROLE_ADMIN dans Keycloak = accès autorisé même si rejected/pending
            if ($detectedRole === 'ROLE_ADMIN') {
                $redirectTo = session('url.intended', '/dashboard');
                session()->forget('url.intended');
                return redirect($redirectTo);
            }

            if ($keycloakUser->isRejected()) {
                return redirect()->route('pending')->with('error', "Votre compte a été rejeté. Veuillez contacter l'administrateur.");
            }

            if ($keycloakUser->isPending() || !$sessionRole) {
                return redirect()->route('pending');
            }

            $redirectTo = session('url.intended', '/dashboard');
            session()->forget('url.intended');

            return redirect($redirectTo);

        } catch (InvalidStateException $e) {
            Log::error('Keycloak InvalidStateException: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Session expirée, veuillez réessayer.');

        } catch (Exception $e) {
            Log::error('Keycloak callback error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Erreur lors de la récupération des données utilisateur : ' . $e->getMessage());
        }
    }

    /**
     * Déconnecte l'utilisateur de la session locale et de Keycloak (SSO).
     */
    public function logout()
    {
        $idToken = session('id_token');
        $baseUrl = config('services.keycloak.base_url', 'http://localhost:8080');
        $realm = config('services.keycloak.realms', 'CompanyRealm');
        $clientId = config('services.keycloak.client_id', 'gestion-client');
        $postLogoutRedirectUri = url('/login');

        // Vérifier si le token en session est bien un ID Token (type 'ID') pour éviter les erreurs de type de token
        $isValidIdToken = false;
        if ($idToken) {
            $parts = explode('.', $idToken);
            if (count($parts) === 3) {
                $payload = json_decode(base64_decode($parts[1]), true);
                if (isset($payload['typ']) && strtolower($payload['typ']) === 'id') {
                    $isValidIdToken = true;
                }
            }
        }

        if ($idToken && $isValidIdToken) {
            // Standard OIDC avec jeton d'identité (déconnexion silencieuse et directe)
            $logoutUrl = "{$baseUrl}/realms/{$realm}/protocol/openid-connect/logout" .
                '?id_token_hint=' . urlencode($idToken) .
                '&post_logout_redirect_uri=' . urlencode($postLogoutRedirectUri) .
                '&client_id=' . urlencode($clientId);
        } else {
            // Déconnexion sans jeton d'identité valide (évite le crash 400 en omettant l'id_token_hint)
            $logoutUrl = "{$baseUrl}/realms/{$realm}/protocol/openid-connect/logout" .
                '?client_id=' . urlencode($clientId) .
                '&post_logout_redirect_uri=' . urlencode($postLogoutRedirectUri);
        }

        // Vider la session locale de l'application
        session()->flush();

        return redirect($logoutUrl);
    }
}
