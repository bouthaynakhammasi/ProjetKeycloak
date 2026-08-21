<?php

namespace App\Http\Controllers;

use App\Services\KeycloakTokenValidator;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;

class KeycloakController extends Controller
{
    /**
     * Redirige l'utilisateur vers la page de connexion Keycloak.
     */
    public function redirect()
    {
        return Socialite::driver('keycloak')->redirect();
    }

    /**
     * Traite le callback OAuth2 de Keycloak et stocke l'utilisateur en session.
     */
    public function callback()
    {
        try {
            $user = Socialite::driver('keycloak')->user();

            $roles = data_get($user->user, 'realm_access.roles', []);
            $primaryRole = is_array($roles) && count($roles) ? $roles[0] : null;

            // Stocker l'ID Token pour la validation de session
            $idToken = $user->accessTokenResponseBody['id_token'] ?? null;

            session([
                'keycloak_user' => [
                    'sub'               => $user->getId(),
                    'name'              => $user->getName(),
                    'email'             => $user->getEmail(),
                    'nickname'          => $user->getNickname(),
                    'preferred_username'=> $user->getNickname(),
                    'token'             => $user->token,
                    'refresh_token'     => $user->refreshToken,
                    'id_token'          => $idToken,
                    'roles'             => $roles,
                    'role'              => $primaryRole,
                ],
            ]);

            return redirect()->route('mailbox.index');
        } catch (\Exception $e) {
            return redirect()->route('keycloak.redirect')
                ->withErrors(['keycloak' => 'Échec de l\'authentification SSO : ' . $e->getMessage()]);
        }
    }

    /**
     * Déconnecte l'utilisateur de l'application.
     * Simplifié : déconnexion locale sans appel à l'endpoint Keycloak complexe.
     */
    public function logout(Request $request)
    {
        // Invalider le cache du token si disponible
        $keycloakUser = session('keycloak_user');
        $idToken = $keycloakUser['id_token'] ?? null;
        
        if ($idToken) {
            $validator = app(KeycloakTokenValidator::class);
            $validator->invalidateTokenCache($idToken);
        }

        // Détruire la session Laravel
        $request->session()->forget('keycloak_user');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Rediriger directement vers la landing page
        return redirect('/');
    }
}
